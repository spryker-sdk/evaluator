<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\SinglePluginArgumentChecker;

use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use SprykerSdk\Evaluator\Checker\CheckerInterface;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Dto\CheckerResponseDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;
use SprykerSdk\Evaluator\Finder\SourceFinderInterface;
use SprykerSdk\Evaluator\Finder\StatementFinderInterface;
use SprykerSdk\Evaluator\Parser\PhpParserInterface;
use Symfony\Component\Finder\Finder;

class SinglePluginArgumentChecker implements CheckerInterface
{
    /**
     * @var string
     */
    public const NAME = 'SINGLE_PLUGIN_ARGUMENT';

    /**
     * @var string
     */
    protected const ANNOTATION_SKIP = '@evaluator-skip-single-plugin-argument';

    /**
     * @var string
     */
    protected const SPRYKER_NAMESPACE_PREFIX = 'Spryker';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE = "Plugin %s has unsupported constructor parameters.\nSupported argument types: int, float, string, const, bool, usage of new statement to\ninstantiate a class (without further methods calls) ";

    /**
     * @var string
     */
    protected const DEPENDENCY_PROVIDER_PATTERN = '*DependencyProvider.php';

    /**
     * @var \SprykerSdk\Evaluator\Finder\SourceFinderInterface
     */
    protected SourceFinderInterface $sourceFinder;

    /**
     * @var \SprykerSdk\Evaluator\Finder\StatementFinderInterface
     */
    protected StatementFinderInterface $statementFinder;

    /**
     * @var \SprykerSdk\Evaluator\Parser\PhpParserInterface
     */
    protected PhpParserInterface $phpParser;

    /**
     * @var string
     */
    protected string $checkerDocUrl;

    /**
     * @param \SprykerSdk\Evaluator\Finder\SourceFinderInterface $sourceFinder
     * @param \SprykerSdk\Evaluator\Finder\StatementFinderInterface $statementFinder
     * @param \SprykerSdk\Evaluator\Parser\PhpParserInterface $phpParser
     * @param string $checkerDocUrl
     */
    public function __construct(
        SourceFinderInterface $sourceFinder,
        StatementFinderInterface $statementFinder,
        PhpParserInterface $phpParser,
        string $checkerDocUrl
    ) {
        $this->sourceFinder = $sourceFinder;
        $this->statementFinder = $statementFinder;
        $this->phpParser = $phpParser;
        $this->checkerDocUrl = $checkerDocUrl;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\CheckerInputDataDto $inputData
     *
     * @return \SprykerSdk\Evaluator\Dto\CheckerResponseDto
     */
    public function check(CheckerInputDataDto $inputData): CheckerResponseDto
    {
        $violations = [];
        $dependencyProviderList = $this->findDependencyProviders($inputData->getPath());
        foreach ($dependencyProviderList as $dependencyProvider) {
            $node = $this->phpParser->parse($dependencyProvider->getPathname());
            $classStatement = $this->statementFinder->findClassStatement($node);
            foreach ($classStatement->getMethods() as $method) {
                $pluginName = $this->getSinglePluginWithArgument($method);
                if ($pluginName === null) {
                    continue;
                }
                $violations[] = new ViolationDto(
                    sprintf(static::ERROR_MESSAGE, $pluginName),
                    sprintf('%s::%s', $classStatement->namespacedName, $method->name),
                );
            }
        }

        return new CheckerResponseDto($violations, $this->checkerDocUrl);
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $method
     *
     * @return string|null
     */
    protected function getSinglePluginWithArgument(ClassMethod $method): ?string
    {
        if (!is_iterable($method->getStmts()) || $this->skipCheck($method)) {
            return null;
        }

        foreach ($method->getStmts() as $stmt) {
            if (!$stmt instanceof Return_ || !$stmt->expr instanceof New_ || !$stmt->expr->class instanceof Name) {
                continue;
            }
            $className = $stmt->expr->class->toString();
            /** @var array<\PhpParser\Node\Arg> $args */
            $args = $stmt->expr->args;
            if (!count($args) || strpos($className, static::SPRYKER_NAMESPACE_PREFIX) !== 0) {
                continue;
            }
            if ($this->isAvailableArguments($args)) {
                continue;
            }

            return $className;
        }

        return null;
    }

    /**
     * @param array<\PhpParser\Node\Arg> $args
     *
     * @return bool
     */
    protected function isAvailableArguments(array $args): bool
    {
        foreach ($args as $arg) {
            $argumentValue = $arg->value;
            if (
                $argumentValue instanceof ConstFetch ||
                $argumentValue instanceof LNumber ||
                $argumentValue instanceof DNumber ||
                $argumentValue instanceof String_ ||
                $argumentValue instanceof New_ ||
                (
                    $argumentValue instanceof Concat &&
                    $this->isAvailableConcatParts($argumentValue)
                )
            ) {
                continue;
            }

            return false;
        }

        return true;
    }

    /**
     * @param \PhpParser\Node\Expr\BinaryOp\Concat $concat
     *
     * @return bool
     */
    protected function isAvailableConcatParts(Concat $concat): bool
    {
        foreach ([$concat->left, $concat->right] as $part) {
            if ($part instanceof String_) {
                continue;
            }
            if ($part instanceof ClassConstFetch) {
                continue;
            }
            if ($part instanceof Concat && $this->isAvailableConcatParts($part)) {
                continue;
            }

            return false;
        }

        return true;
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $method
     *
     * @return bool
     */
    protected function skipCheck(ClassMethod $method): bool
    {
        return $method->getDocComment() &&
            strpos($method->getDocComment()->getText(), static::ANNOTATION_SKIP) !== false;
    }

    /**
     * @param string $path
     *
     * @return \Symfony\Component\Finder\Finder
     */
    protected function findDependencyProviders(string $path): Finder
    {
        return $this->sourceFinder->find([static::DEPENDENCY_PROVIDER_PATTERN], [$path]);
    }
}
