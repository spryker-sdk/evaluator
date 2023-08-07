<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Fetcher;

use SprykerSdk\Evaluator\Checker\CheckerInterface;
use SprykerSdk\Evaluator\Checker\CheckerRegistryInterface;
use SprykerSdk\Evaluator\Dto\EvaluatorInputDataDto;

class CheckerFetcher implements CheckerFetcherInterface
{
    /**
     * @var \SprykerSdk\Evaluator\Checker\CheckerRegistryInterface
     */
    private CheckerRegistryInterface $checkerRegistry;

    /**
     * @param \SprykerSdk\Evaluator\Checker\CheckerRegistryInterface $checkerRegistry
     */
    public function __construct(CheckerRegistryInterface $checkerRegistry)
    {
        $this->checkerRegistry = $checkerRegistry;
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\EvaluatorInputDataDto $inputData
     *
     * @return array<\SprykerSdk\Evaluator\Checker\CheckerInterface>
     */
    public function getCheckersFilteredByInputData(EvaluatorInputDataDto $inputData): array
    {
        $checkers = $this->checkerRegistry->getAllCheckers();

        return array_values($this->filterAllowedCheckers($inputData, $this->filterExcludedCheckers($inputData, $checkers)));
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\EvaluatorInputDataDto $inputData
     * @param array<\SprykerSdk\Evaluator\Checker\CheckerInterface> $checkers
     *
     * @return array<\SprykerSdk\Evaluator\Checker\CheckerInterface>
     */
    protected function filterAllowedCheckers(EvaluatorInputDataDto $inputData, array $checkers): array
    {
        if (count($inputData->getCheckerNames()) === 0) {
            return $checkers;
        }

        return array_filter(
            $checkers,
            static fn (CheckerInterface $checker): bool => in_array($checker->getName(), $inputData->getCheckerNames(), true)
        );
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\EvaluatorInputDataDto $inputData
     * @param array<\SprykerSdk\Evaluator\Checker\CheckerInterface> $checkers
     *
     * @return array<\SprykerSdk\Evaluator\Checker\CheckerInterface>
     */
    protected function filterExcludedCheckers(EvaluatorInputDataDto $inputData, array $checkers): array
    {
        return array_filter(
            $checkers,
            static fn (CheckerInterface $checker): bool => !in_array($checker->getName(), $inputData->getExcludedCheckerNames(), true)
        );
    }
}
