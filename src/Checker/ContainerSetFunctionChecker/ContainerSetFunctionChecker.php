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

    protected SourceFinderInterface $sourceFinder;

    protected PhpParserInterface $phpParser;

    protected NodeFinderInterface $nodeFinder;

    protected string $checkerDocUrl;

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

    public function check(CheckerInputDataDto $inputData): CheckerResponseDto
    {
        $violations = [];
        $dependencyProviderList = $this->findDependencyProviders($inputData->getPath());
        foreach ($dependencyProviderList as $dependencyProvider) {
            $violations = [...$violations, ...$this->getViolationFromFile($dependencyProvider)];
        }

        return new CheckerResponseDto($violations, $this->checkerDocUrl);
    }

    public function getName(): string
    {
        return static::NAME;
    }

    /**
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

    protected function isContainArray(Return_ $returnStmt): bool
    {
        return $returnStmt->expr instanceof Array_;
    }

    protected function findDependencyProviders(string $path): Finder
    {
        return $this->sourceFinder->find([static::DEPENDENCY_PROVIDER_PATTERN], [$path], static::EXCLUDE_PATH_LIST);
    }
}
