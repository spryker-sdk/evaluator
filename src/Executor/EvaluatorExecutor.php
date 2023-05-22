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
use SprykerSdk\Evaluator\Dto\EvaluatorInputDataDto;
use SprykerSdk\Evaluator\Dto\ReportDto;
use SprykerSdk\Evaluator\Dto\ReportLineDto;
use SprykerSdk\Evaluator\Report\ReportSendProcessorInterface;

class EvaluatorExecutor implements EvaluatorExecutorInterface
{
    /**
     * @var \SprykerSdk\Evaluator\Checker\CheckerRegistryInterface
     */
    protected CheckerRegistryInterface $checkerRegistry;

    /**
     * @var \SprykerSdk\Evaluator\Report\ReportSendProcessorInterface
     */
    protected ReportSendProcessorInterface $reportSendProcessor;

    /**
     * @param \SprykerSdk\Evaluator\Checker\CheckerRegistryInterface $checkerRegistry
     * @param \SprykerSdk\Evaluator\Report\ReportSendProcessorInterface $reportSendProcessor
     */
    public function __construct(CheckerRegistryInterface $checkerRegistry, ReportSendProcessorInterface $reportSendProcessor)
    {
        $this->checkerRegistry = $checkerRegistry;
        $this->reportSendProcessor = $reportSendProcessor;
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\EvaluatorInputDataDto $inputData
     *
     * @return \SprykerSdk\Evaluator\Dto\ReportDto
     */
    public function execute(EvaluatorInputDataDto $inputData): ReportDto
    {
        $report = new ReportDto();

        foreach ($this->getCheckers($inputData) as $checker) {
            $checkerResponse = $checker->check(new CheckerInputDataDto($inputData->getPath()));

            $report->addReportLine(
                new ReportLineDto($checker->getName(), $checkerResponse->getViolations(), $checkerResponse->getDocUrl()),
            );
        }

        $this->reportSendProcessor->process($report);

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
