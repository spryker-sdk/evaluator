<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Executor;

use SprykerSdk\Evaluator\Checker\CheckerRegistryInterface;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Dto\DebugInfoDto;
use SprykerSdk\Evaluator\Dto\EvaluatorInputDataDto;
use SprykerSdk\Evaluator\Dto\ReportDto;
use SprykerSdk\Evaluator\Dto\ReportLineDto;
use SprykerSdk\Evaluator\Fetcher\CheckerFetcherInterface;
use SprykerSdk\Evaluator\Report\ReportSendProcessorInterface;
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
     * @var \SprykerSdk\Evaluator\Report\ReportSendProcessorInterface
     */
    protected ReportSendProcessorInterface $reportSendProcessor;

    /**
     * @var \SprykerSdk\Evaluator\Fetcher\CheckerFetcherInterface
     */
    private CheckerFetcherInterface $checkerFetcher;

    /**
     * @param \SprykerSdk\Evaluator\Checker\CheckerRegistryInterface $checkerRegistry
     * @param \SprykerSdk\Evaluator\Stopwatch\StopwatchFactory $stopwatchFactory
     * @param \SprykerSdk\Evaluator\Report\ReportSendProcessorInterface $reportSendProcessor
     * @param \SprykerSdk\Evaluator\Fetcher\CheckerFetcherInterface $checkerFetcher
     */
    public function __construct(
        CheckerRegistryInterface $checkerRegistry,
        StopwatchFactory $stopwatchFactory,
        ReportSendProcessorInterface $reportSendProcessor,
        CheckerFetcherInterface $checkerFetcher
    ) {
        $this->checkerRegistry = $checkerRegistry;
        $this->stopwatchFactory = $stopwatchFactory;
        $this->reportSendProcessor = $reportSendProcessor;
        $this->checkerFetcher = $checkerFetcher;
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

        foreach ($this->checkerFetcher->getCheckersFilteredByInputData($inputData) as $checker) {
            if (!$checker->isApplicable()) {
                continue;
            }

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

        $this->reportSendProcessor->process($report);

        return $report;
    }
}
