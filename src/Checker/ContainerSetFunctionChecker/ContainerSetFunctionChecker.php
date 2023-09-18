<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\ContainerSetFunctionChecker;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use SprykerSdk\Evaluator\Checker\AbstractChecker;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Dto\CheckerResponseDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;
use SprykerSdk\Evaluator\Finder\SourceFinderInterface;
use SprykerSdk\Evaluator\Parser\NodeFinderInterface;
use SprykerSdk\Evaluator\Parser\PhpParserInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ContainerSetFunctionChecker extends AbstractChecker
{
    /**
     * @var string
     */
    public const NAME = 'CONTAINER_SET_FUNCTION_CHECKER';

    /**
     * @var string
     */
    protected const DEPENDENCY_PROVIDER_PATTERN = '*DependencyProvider.php';

    /**
     * @var array<string>
     */
    protected const EXCLUDE_PATH_LIST = ['vendor'];

    /**
     * @var string
     */
    protected const VIOLATION_MESSAGE = 'The callback function inside `container->set()` should not return an array directly but instead call another method. Please review your code and make the necessary changes.';

    /**
     * @var \SprykerSdk\Evaluator\Finder\SourceFinderInterface
     */
    protected SourceFinderInterface $sourceFinder;

    /**
     * @var \SprykerSdk\Evaluator\Parser\PhpParserInterface
     */
    protected PhpParserInterface $phpParser;

    /**
     * @var \SprykerSdk\Evaluator\Parser\NodeFinderInterface
     */
    protected NodeFinderInterface $nodeFinder;

    /**
     * @var string
     */
    protected string $checkerDocUrl;

    /**
     * @param \SprykerSdk\Evaluator\Finder\SourceFinderInterface $sourceFinder
     * @param \SprykerSdk\Evaluator\Parser\PhpParserInterface $phpParser
     * @param \SprykerSdk\Evaluator\Parser\NodeFinderInterface $nodeFinder
     * @param string $checkerDocUrl
     */
    public function __construct(
        SourceFinderInterface $sourceFinder,
        PhpParserInterface $phpParser,
        NodeFinderInterface $nodeFinder,
        string $checkerDocUrl
    ) {
        $this->sourceFinder = $sourceFinder;
        $this->phpParser = $phpParser;
        $this->nodeFinder = $nodeFinder;
        $this->checkerDocUrl = $checkerDocUrl;
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
            $violations = [...$violations, ...$this->getViolationFromFile($dependencyProvider)];
        }

        return new CheckerResponseDto($violations, $this->checkerDocUrl);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $fileInfo
     *
     * @return array<\SprykerSdk\Evaluator\Dto\ViolationDto>
     */
    protected function getViolationFromFile(SplFileInfo $fileInfo): array
    {
        $violations = [];
        $fileStm = $this->phpParser->parse($fileInfo->getPathname());
        $containerSetStmList = $this->findContainerSetStm($fileStm);
        $returnStmList = $this->findReturnStm($containerSetStmList);

        foreach ($returnStmList as $returnStm) {
            if (!$this->isContainArray($returnStm)) {
                continue;
            }

            $violations[] = new ViolationDto(
                static::VIOLATION_MESSAGE,
                sprintf('%s:%s', $fileInfo->getPathname(), $returnStm->getLine()),
            );
        }

        return $violations;
    }

    /**
     * @param array<\PhpParser\Node> $syntaxTree
     *
     * @return array<\PhpParser\Node>
     */
    protected function findContainerSetStm(array $syntaxTree): array
    {
        return $this->nodeFinder->find($syntaxTree, function ($statement) {
            return $statement instanceof Expression
                && $statement->expr instanceof MethodCall
                && $statement->expr->var instanceof Variable
                && $statement->expr->var->name === 'container'
                && $statement->expr->name->name === 'set';
        });
    }

    /**
     * @param array<\PhpParser\Node> $syntaxTree
     *
     * @return array<\PhpParser\Node\Stmt\Return_>
     */
    protected function findReturnStm(array $syntaxTree): array
    {
        /** @var array<\PhpParser\Node\Stmt\Return_> $nodes */
        $nodes = $this->nodeFinder->findInstanceOf($syntaxTree, Return_::class);

        return $nodes;
    }

    /**
     * @param \PhpParser\Node\Stmt\Return_ $returnStmt
     *
     * @return bool
     */
    protected function isContainArray(Return_ $returnStmt): bool
    {
        return $returnStmt->expr instanceof Array_;
    }

    /**
     * @param string $path
     *
     * @return \Symfony\Component\Finder\Finder
     */
    protected function findDependencyProviders(string $path): Finder
    {
        return $this->sourceFinder->find([static::DEPENDENCY_PROVIDER_PATTERN], [$path], static::EXCLUDE_PATH_LIST);
    }
}
