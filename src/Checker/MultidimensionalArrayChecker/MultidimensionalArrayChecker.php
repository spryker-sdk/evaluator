<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\MultidimensionalArrayChecker;

use PhpParser\Node\Stmt\ClassMethod;
use SprykerSdk\Evaluator\Checker\CheckerInterface;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Dto\CheckerResponseDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;
use SprykerSdk\Evaluator\Finder\SourceFinderInterface;
use SprykerSdk\Evaluator\Finder\StatementFinderInterface;
use SprykerSdk\Evaluator\Parser\PhpParserInterface;
use Symfony\Component\Finder\Finder;

class MultidimensionalArrayChecker implements CheckerInterface
{
    /**
     * @var string
     */
    public const NAME = 'MULTIDIMENSIONAL_ARRAY_CHECKER';

    /**
     * @var string
     */
    protected const ANNOTATION_SKIP = '@evaluator-skip-multidimensional-array';

    /**
     * @var int
     */
    protected const MAX_LEVEL = 2;

    /**
     * @var string
     */
    protected const ERROR_MESSAGE = "Reached max level of nesting for the plugin registration in the {%s::%s()}.\nThe maximum allowed nesting level is %s. Please, refactor code, otherwise it will cause upgradability issues in the future.";

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
     * @var array<\SprykerSdk\Evaluator\Checker\MultidimensionalArrayChecker\NestingStructure\NestingStructureInterface>
     */
    protected array $nestingStructures;

    /**
     * @var string
     */
    protected string $checkerDocUrl;

    /**
     * @param \SprykerSdk\Evaluator\Finder\SourceFinderInterface $sourceFinder
     * @param \SprykerSdk\Evaluator\Finder\StatementFinderInterface $statementFinder
     * @param \SprykerSdk\Evaluator\Parser\PhpParserInterface $phpParser
     * @param array<\SprykerSdk\Evaluator\Checker\MultidimensionalArrayChecker\NestingStructure\NestingStructureInterface> $nestingStructures
     * @param string $checkerDocUrl
     */
    public function __construct(
        SourceFinderInterface $sourceFinder,
        StatementFinderInterface $statementFinder,
        PhpParserInterface $phpParser,
        array $nestingStructures,
        string $checkerDocUrl = ''
    ) {
        $this->sourceFinder = $sourceFinder;
        $this->statementFinder = $statementFinder;
        $this->phpParser = $phpParser;
        $this->nestingStructures = $nestingStructures;
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
            $statement = $this->phpParser->parse($dependencyProvider->getPathname());
            $classStatement = $this->statementFinder->findClassStatement($statement);
            foreach ($classStatement->getMethods() as $method) {
                if ($this->skipCheck($method) || $this->countLevelArrayMultidimensional($method) <= static::MAX_LEVEL) {
                    continue;
                }
                $violations[] = new ViolationDto(
                    sprintf(static::ERROR_MESSAGE, $classStatement->name, $method->name, static::MAX_LEVEL),
                    (string)$classStatement->namespacedName,
                );
            }
        }

        return new CheckerResponseDto($violations, $this->checkerDocUrl);
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
     * @param \PhpParser\Node\Stmt\ClassMethod $method
     *
     * @return int
     */
    protected function countLevelArrayMultidimensional(ClassMethod $method): int
    {
        $maxLevel = 0;

        if (!is_iterable($method->getStmts())) {
            return $maxLevel;
        }
        foreach ($method->getStmts() as $stmt) {
            foreach ($this->nestingStructures as $nestingStructure) {
                if (!$nestingStructure->isApplicable($stmt)) {
                    continue;
                }
                $currentArrayLevel = $nestingStructure->getDepth($stmt);
                if ($currentArrayLevel > $maxLevel) {
                    $maxLevel = $currentArrayLevel;
                }
                if ($maxLevel > static::MAX_LEVEL) {
                    return $maxLevel;
                }
            }
        }

        return $maxLevel;
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
