<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Report;

use Psr\Log\LoggerInterface;
use SprykerSdk\Evaluator\Dto\ReportDto;
use SprykerSdk\Evaluator\Report\Builder\ReportDtoBuilderInterface;
use SprykerSdk\Evaluator\Report\Sender\ReportSenderInterface;
use Throwable;

class OperationsReportSendProcessor implements ReportSendProcessorInterface
{
    /**
     * @var bool
     */
    protected bool $isReportingEnabled;

    /**
     * @var \SprykerSdk\Evaluator\Report\Builder\ReportDtoBuilderInterface
     */
    protected ReportDtoBuilderInterface $reportDtoBuilder;

    /**
     * @var \SprykerSdk\Evaluator\Report\Sender\ReportSenderInterface
     */
    protected ReportSenderInterface $reportSender;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param bool $isReportingEnabled
     * @param \SprykerSdk\Evaluator\Report\Builder\ReportDtoBuilderInterface $reportDtoBuilder
     * @param \SprykerSdk\Evaluator\Report\Sender\ReportSenderInterface $reportSender
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        bool $isReportingEnabled,
        ReportDtoBuilderInterface $reportDtoBuilder,
        ReportSenderInterface $reportSender,
        LoggerInterface $logger
    ) {
        $this->isReportingEnabled = $isReportingEnabled;
        $this->reportDtoBuilder = $reportDtoBuilder;
        $this->reportSender = $reportSender;
        $this->logger = $logger;
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\ReportDto $evaluatorReportDto
     *
     * @return void
     */
    public function process(ReportDto $evaluatorReportDto): void
    {
        if (!$this->isReportingEnabled) {
            return;
        }

        try {
            $reportDto = $this->reportDtoBuilder->buildReportDto($evaluatorReportDto);

            $this->reportSender->send($reportDto);
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}
