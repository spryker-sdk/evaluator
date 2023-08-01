<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Checker\NpmChecker;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Checker\NpmChecker\NpmAuditExecutor;
use SprykerSdk\Evaluator\Checker\NpmChecker\NpmChecker;
use SprykerSdk\Evaluator\Checker\NpmChecker\NpmExecutorException;
use SprykerSdk\Evaluator\Checker\NpmChecker\NpmInstallationValidator;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;

class NpmCheckerTest extends TestCase
{
    /**
     * @return void
     */
    public function testIsApplicableShouldReturnIfNpmInstalled(): void
    {
        //Arrange
        $npmInstallationValidatorMock = $this->createNpmInstallationValidatorMock();
        $npmAuditExecutorMock = $this->createNpmAuditExecutorMock([]);
        $npmChecker = new NpmChecker($npmInstallationValidatorMock, $npmAuditExecutorMock);

        //Act
        $result = $npmChecker->isApplicable();

        //Assert
        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function testIsApplicableShouldReturnNpmIssueWhenThrownException(): void
    {
        //Arrange
        $npmInstallationValidatorMock = $this->createNpmInstallationValidatorMock();
        $npmAuditExecutorMock = $this->createNpmAuditExecutorMock([], true);
        $inputData = new CheckerInputDataDto('');
        $npmChecker = new NpmChecker($npmInstallationValidatorMock, $npmAuditExecutorMock);

        //Act
        $response = $npmChecker->check($inputData);

        //Assert
        $this->assertCount(1, $response->getViolations());
        $violationDto = $response->getViolations()[0];

        $this->assertStringStartsWith(NpmChecker::NPM_ISSUE_MESSAGE_PREFIX, $violationDto->getMessage());
    }

    /**
     * @return void
     */
    public function testIsApplicableShouldReturnValidResponse(): void
    {
        //Arrange
        $npmInstallationValidatorMock = $this->createNpmInstallationValidatorMock();
        $violationDto = new ViolationDto('some message', 'target');
        $npmAuditExecutorMock = $this->createNpmAuditExecutorMock([$violationDto]);
        $inputData = new CheckerInputDataDto('');
        $npmChecker = new NpmChecker($npmInstallationValidatorMock, $npmAuditExecutorMock);

        //Act
        $response = $npmChecker->check($inputData);

        //Assert
        $this->assertCount(1, $response->getViolations());
        $violationDto = $response->getViolations()[0];

        $this->assertSame($violationDto, $violationDto);
    }

    /**
     * @return \SprykerSdk\Evaluator\Checker\NpmChecker\NpmInstallationValidator
     */
    public function createNpmInstallationValidatorMock(): NpmInstallationValidator
    {
        $npmInstallationValidator = $this->createMock(NpmInstallationValidator::class);
        $npmInstallationValidator->method('isNpmInstalled')->willReturn(true);

        return $npmInstallationValidator;
    }

    /**
     * @param array<\SprykerSdk\Evaluator\Dto\ViolationDto> $violations
     * @param bool $throwException
     *
     * @return \SprykerSdk\Evaluator\Checker\NpmChecker\NpmAuditExecutor
     */
    public function createNpmAuditExecutorMock(array $violations, bool $throwException = false): NpmAuditExecutor
    {
        $npmAuditExecutor = $this->createMock(NpmAuditExecutor::class);

        if ($throwException) {
            $npmAuditExecutor->method('executeNpmAudit')->willThrowException(new NpmExecutorException('Some issue'));
        }

        $npmAuditExecutor->method('executeNpmAudit')->willReturn($violations);

        return $npmAuditExecutor;
    }
}
