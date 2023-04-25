<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\DependencyProviderAdditionalLogicChecker;

use SprykerSdk\Evaluator\Checker\CheckerInterface;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Finder\SourceFinderInterface;
use SprykerSdk\Evaluator\Finder\StatementFinderInterface;
use SprykerSdk\Evaluator\Parser\PhpParserInterface;
use Symfony\Component\Finder\Finder;

class DependencyProviderAdditionalLogicChecker implements CheckerInterface
{
    /**
     * @var string
     */
    protected const NAME = 'DEPENDENCY_PROVIDER_ADDITIONAL_LOGIC_CHECKER';

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
     * @param \SprykerSdk\Evaluator\Finder\SourceFinderInterface $sourceFinder
     * @param \SprykerSdk\Evaluator\Finder\StatementFinderInterface $statementFinder
     * @param \SprykerSdk\Evaluator\Parser\PhpParserInterface $phpParser
     */
    public function __construct(
        SourceFinderInterface $sourceFinder,
        StatementFinderInterface $statementFinder,
        PhpParserInterface $phpParser
    ) {
        $this->sourceFinder = $sourceFinder;
        $this->statementFinder = $statementFinder;
        $this->phpParser = $phpParser;
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\CheckerInputDataDto $inputData
     *
     * @return array<\SprykerSdk\Evaluator\Dto\ViolationDto>
     */
    public function check(CheckerInputDataDto $inputData): array
    {
        $violations = [];
        $dependencyProviderList = $this->findDependencyProviders($inputData->getPath());

        foreach ($dependencyProviderList as $dependencyProvider) {
            $statement = $this->phpParser->parse($dependencyProvider->getPathname());
            $classStatement = $this->statementFinder->findClassStatement($statement);
            // TODO: check additional logic
        }

        return $violations;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
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
