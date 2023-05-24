<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Executor;

use SprykerSdk\Evaluator\Checker\CheckerInterface;
use SprykerSdk\Evaluator\Checker\CheckerRegistryInterface;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Dto\DebugInfoDto;
use SprykerSdk\Evaluator\Dto\EvaluatorInputDataDto;
use SprykerSdk\Evaluator\Dto\ReportDto;
use SprykerSdk\Evaluator\Dto\ReportLineDto;
use SprykerSdk\Evaluator\Stopwatch\StopwatchFactory;

class EvaluatorExecutor implements EvaluatorExecutorInterface
{
    /**
     * @var \SprykerSdk\Evaluator\Checker\CheckerRegistryInterface
     */
    protected CheckerRegistryInterface $checkerRegistry;

    /**
     * @var \SprykerSdk\Evaluator\Stopwatch\StopwatchFactory
     */
    protected StopwatchFactory $stopwatchFactory;

    /**
     * @param \SprykerSdk\Evaluator\Checker\CheckerRegistryInterface $checkerRegistry
     * @param \SprykerSdk\Evaluator\Stopwatch\StopwatchFactory $stopwatchFactory
     */
    public function __construct(CheckerRegistryInterface $checkerRegistry, StopwatchFactory $stopwatchFactory)
    {
        $this->checkerRegistry = $checkerRegistry;
        $this->stopwatchFactory = $stopwatchFactory;
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\EvaluatorInputDataDto $inputData
     *
     * @return \SprykerSdk\Evaluator\Dto\ReportDto
     */
    public function execute(EvaluatorInputDataDto $inputData): ReportDto
    {
        $report = new ReportDto();
        $stopWatch = $this->stopwatchFactory->getStopWatch();

        foreach ($this->getCheckers($inputData) as $checker) {
            $stopWatch->start($checker->getName());

            $checkerResponse = $checker->check(new CheckerInputDataDto($inputData->getPath()));

            $event = $stopWatch->stop($checker->getName());

            $report->addReportLine(
                new ReportLineDto(
                    $checker->getName(),
                    $checkerResponse->getViolations(),
                    $checkerResponse->getDocUrl(),
                    new DebugInfoDto((int)$event->getDuration(), $event->getMemory()),
                ),
            );
        }

        return $report;
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\EvaluatorInputDataDto $inputData
     *
     * @return array<\SprykerSdk\Evaluator\Checker\CheckerInterface>
     */
    protected function getCheckers(EvaluatorInputDataDto $inputData): array
    {
        if (count($inputData->getCheckerNames()) === 0) {
            return $this->checkerRegistry->getAllCheckers();
        }

        return array_map(
            fn (string $name): CheckerInterface => $this->checkerRegistry->getCheckerByName($name),
            $inputData->getCheckerNames(),
        );
    }
}
