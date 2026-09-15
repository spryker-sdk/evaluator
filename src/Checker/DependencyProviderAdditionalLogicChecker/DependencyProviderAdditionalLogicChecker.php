<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\DependencyProviderAdditionalLogicChecker;

use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeFinder;
use SprykerSdk\Evaluator\Checker\AbstractChecker;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Dto\CheckerResponseDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;
use SprykerSdk\Evaluator\Finder\SourceFinderInterface;
use SprykerSdk\Evaluator\Parser\PhpParserInterface;
use Symfony\Component\Finder\Finder;

class DependencyProviderAdditionalLogicChecker extends AbstractChecker
{
    /**
     * @var string
     */
    public const NAME = 'DEPENDENCY_PROVIDER_ADDITIONAL_LOGIC_CHECKER';

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
    protected const CONDITION_SUFFIX = '}';

    protected SourceFinderInterface $sourceFinder;

    protected PhpParserInterface $phpParser;

    protected string $checkerDocUrl;

    public function __construct(
        SourceFinderInterface $sourceFinder,
        PhpParserInterface $phpParser,
        string $checkerDocUrl
    ) {
        $this->sourceFinder = $sourceFinder;
        $this->phpParser = $phpParser;
        $this->checkerDocUrl = $checkerDocUrl;
    }

    public function check(CheckerInputDataDto $inputData): CheckerResponseDto
    {
        $violations = [];
        $dependencyProviderList = $this->findDependencyProviders($inputData->getPath());
        foreach ($dependencyProviderList as $dependencyProvider) {
            $fileStm = $this->phpParser->parse($dependencyProvider->getPathname());
            $conditionStmList = $this->findConditionStatement($fileStm);

            /** @var \PhpParser\Node\Stmt\If_ $conditionStm */
            foreach ($conditionStmList as $conditionStm) {
                if ($this->isAcceptedCondition($conditionStm)) {
                    continue;
                }

                $conditionString = $this->getConditionString($conditionStm, $dependencyProvider->getContents());
                $violations[] = new ViolationDto(
                    sprintf('The %s condition statement is forbidden in DependencyProvider', $conditionString),
                    $dependencyProvider->getPathname(),
                );
            }
        }

        return new CheckerResponseDto($violations, $this->checkerDocUrl);
    }

    public function getName(): string
    {
        return static::NAME;
    }

    protected function findDependencyProviders(string $path): Finder
    {
        return $this->sourceFinder->find([static::DEPENDENCY_PROVIDER_PATTERN], [$path], static::EXCLUDE_PATH_LIST);
    }

    /**
     * @param array<\PhpParser\Node> $syntaxTree
     *
     * @return array<\PhpParser\Node>
     */
    public function findConditionStatement(array $syntaxTree): array
    {
        return (new NodeFinder())->findInstanceOf($syntaxTree, If_::class);
    }

    protected function isAcceptedCondition(If_ $conditionStm): bool
    {
        if ($conditionStm->cond instanceof MethodCall && $this->isDevelopmentMethodCall($conditionStm->cond)) {
            return true;
        }

        if ($conditionStm->cond instanceof FuncCall && $this->isClassExistsFuncCall($conditionStm->cond)) {
            return true;
        }

        return false;
    }

    protected function isDevelopmentMethodCall(MethodCall $methodCall): bool
    {
        return $methodCall->name instanceof Identifier && preg_match('/^is.*Development.*/', $methodCall->name->name);
    }

    protected function isClassExistsFuncCall(FuncCall $funcCall): bool
    {
        return $funcCall->name instanceof Name && $funcCall->name->name == 'class_exists';
    }

    protected function getConditionString(If_ $conditionStm, string $fileBody): ?string
    {
        $lineList = array_filter(
            explode(PHP_EOL, $fileBody),
            function ($key) use ($conditionStm) {
                return $key >= $conditionStm->cond->getAttribute('startLine') - 1 &&
                    $key <= $conditionStm->cond->getAttribute('endLine') - 1;
            },
            ARRAY_FILTER_USE_KEY,
        );

        return trim(implode(PHP_EOL, $lineList)) . static::CONDITION_SUFFIX;
    }
}
