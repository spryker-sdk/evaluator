<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Report;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;
use SprykerSdk\Evaluator\Dto\ReportDto as EvaluatorReportDto;
use SprykerSdk\Evaluator\Report\Builder\ReportDtoBuilderInterface;
use SprykerSdk\Evaluator\Report\Dto\ReportDto;
use SprykerSdk\Evaluator\Report\OperationsReportSendProcessor;
use SprykerSdk\Evaluator\Report\Sender\ReportSenderInterface;

/**
 * @group SprykerSdkTest
 * @group Evaluator
 * @group Unit
 * @group Report
 * @group OperationsReportSendProcessorTest
 */
class OperationsReportSendProcessorTest extends TestCase
{
    /**
     * @return void
     */
    public function testProcessShouldSkipWhenReportingDisabled(): void
    {
        // Arrange
        $operationsReportSendProcessor = new OperationsReportSendProcessor(
            false,
            $this->createReportDtoBuilderMock(),
            $this->createReportSenderMock(false),
            $this->createLoggerMock(false),
        );

        $evaluatorReportDto = new EvaluatorReportDto([]);

        // Act
        $operationsReportSendProcessor->process($evaluatorReportDto);
    }

    /**
     * @return void
     */
    public function testProcessShouldSendReport(): void
    {
        // Arrange
        $operationsReportSendProcessor = new OperationsReportSendProcessor(
            true,
            $this->createReportDtoBuilderMock(),
            $this->createReportSenderMock(true),
            $this->createLoggerMock(false),
        );

        $evaluatorReportDto = new EvaluatorReportDto([]);

        // Act
        $operationsReportSendProcessor->process($evaluatorReportDto);
    }

    /**
     * @return void
     */
    public function testProcessShouldLogErrorWhenExceptionThrown(): void
    {
        // Arrange
        $operationsReportSendProcessor = new OperationsReportSendProcessor(
            true,
            $this->createReportDtoBuilderMock(),
            $this->createReportSenderMock(true, true),
            $this->createLoggerMock(true),
        );

        $evaluatorReportDto = new EvaluatorReportDto([]);

        // Act
        $operationsReportSendProcessor->process($evaluatorReportDto);
    }

    /**
     * @return \SprykerSdk\Evaluator\Report\Builder\ReportDtoBuilderInterface
     */
    protected function createReportDtoBuilderMock(): ReportDtoBuilderInterface
    {
        $reportDtoBuilder = $this->createMock(ReportDtoBuilderInterface::class);

        $reportDtoBuilder->method('buildReportDto')->willReturn($this->createMock(ReportDto::class));

        return $reportDtoBuilder;
    }

    /**
     * @param bool $shouldBeSent
     * @param bool $throwException
     *
     * @return \SprykerSdk\Evaluator\Report\Sender\ReportSenderInterface
     */
    protected function createReportSenderMock(bool $shouldBeSent, bool $throwException = false): ReportSenderInterface
    {
        $reportSender = $this->createMock(ReportSenderInterface::class);

        $method = $reportSender->expects($shouldBeSent ? $this->once() : $this->never())->method('send');

        if ($throwException) {
            $method->willThrowException(new RuntimeException(''));
        }

        return $reportSender;
    }

    /**
     * @param bool $shouldTriggerLogging
     *
     * @return \Psr\Log\LoggerInterface
     */
    protected function createLoggerMock(bool $shouldTriggerLogging): LoggerInterface
    {
        $logger = $this->createMock(LoggerInterface::class);

        $logger->expects($shouldTriggerLogging ? $this->once() : $this->never())->method('error');

        return $logger;
    }
}
