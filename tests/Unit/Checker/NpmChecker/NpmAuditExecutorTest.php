<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Checker\NpmChecker;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Checker\NpmChecker\NpmAuditExecutor;
use SprykerSdk\Evaluator\Checker\NpmChecker\NpmExecutorException;
use SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface;
use Symfony\Component\Process\Process;

class NpmAuditExecutorTest extends TestCase
{
    /**
     * @return void
     */
    public function testExecuteNpmAuditShouldReturnEmptyArrayWhenAuditIsSuccessful(): void
    {
        //Arrange
        $processMock = $this->createProcessMock('', '', true);
        $processRunnerMock = $this->createProcessRunnerMock($processMock);
        $npmAuditExecutor = new NpmAuditExecutor($processRunnerMock);

        //Act
        $result = $npmAuditExecutor->executeNpmAudit();

        //Assert
        $this->assertEmpty($result);
    }

    /**
     * @return void
     */
    public function testExecuteNpmAuditShouldThrowExceptionWhenProcessReturnedError(): void
    {
        //Arrange
        $processMock = $this->createProcessMock('', 'Some Error', false);
        $processRunnerMock = $this->createProcessRunnerMock($processMock);
        $npmAuditExecutor = new NpmAuditExecutor($processRunnerMock);

        $this->expectException(NpmExecutorException::class);
        $this->expectExceptionMessage('Some Error');

        //Act
        $npmAuditExecutor->executeNpmAudit();
    }

    /**
     * @return void
     */
    public function testExecuteNpmAuditShouldThrowExceptionWhenInvalidJsonReturned(): void
    {
        //Arrange
        $processMock = $this->createProcessMock('{Invalid:json');
        $processRunnerMock = $this->createProcessRunnerMock($processMock);
        $npmAuditExecutor = new NpmAuditExecutor($processRunnerMock);

        $this->expectException(NpmExecutorException::class);

        //Act
        $npmAuditExecutor->executeNpmAudit();
    }

    /**
     * @dataProvider invalidJsonFormatReceivedDataProvider
     *
     * @param string $toolOutput
     *
     * @return void
     */
    public function testExecuteNpmAuditShouldThrowExceptionWhenUnexpectedJsonReturned(string $toolOutput): void
    {
        //Arrange
        $processMock = $this->createProcessMock($toolOutput);
        $processRunnerMock = $this->createProcessRunnerMock($processMock);
        $npmAuditExecutor = new NpmAuditExecutor($processRunnerMock);

        $this->expectException(NpmExecutorException::class);

        //Act
        $npmAuditExecutor->executeNpmAudit();
    }

    /**
     * @dataProvider vulnerabilitiesThatShouldBeSkippedDataProvider
     *
     * @param string $toolOutput
     *
     * @return void
     */
    public function testExecuteNpmAuditShouldSkipVulnerabilities(string $toolOutput): void
    {
        //Arrange
        $processMock = $this->createProcessMock($toolOutput);
        $processRunnerMock = $this->createProcessRunnerMock($processMock);
        $npmAuditExecutor = new NpmAuditExecutor($processRunnerMock);

        //Act
        $result = $npmAuditExecutor->executeNpmAudit(['critical']);

        //Assert
        $this->assertEmpty($result);
    }

    /**
     * @return void
     */
    public function testExecuteNpmAuditShouldFetchUniqueDataFromNpmAudit(): void
    {
        //Arrange
        $processMock = $this->createProcessMock(
            '{"vulnerabilities": {
            "datatables.net": {"name": "datatables.net", "severity": "critical", "via": [{"title": "Test violation", "url": "https://violation-url"}, {"title": "Test violation", "url": "https://violation-url"}]}
            }}',
        );
        $processRunnerMock = $this->createProcessRunnerMock($processMock);
        $npmAuditExecutor = new NpmAuditExecutor($processRunnerMock);

        //Act
        $result = $npmAuditExecutor->executeNpmAudit();

        //Assert
        $this->assertCount(1, $result);
        $violationDto = $result[0];

        $this->assertSame("[critical] Test violation\nhttps://violation-url", $violationDto->getMessage());
        $this->assertSame('datatables.net', $violationDto->getTarget());
    }

    /**
     * @return array<string, array<string>>
     */
    public static function invalidJsonFormatReceivedDataProvider(): array
    {
        return [
            'missedVulnerabilitiesKey' => ['{}'],
            'nonArrayVulnerabilitiesKey' => ['{"vulnerabilities": "non-array"}'],
            'missedNameKey' => ['{"vulnerabilities": {"datatables.net": {"severity": "critical", "via": {"title": "someTitle"}}}}'],
            'missedSeverityKey' => ['{"vulnerabilities": {"datatables.net": {"name": "datatables.net", "via": {"title": "someTitle"}}}}'],
            'missedViaKey' => ['{"vulnerabilities": {"datatables.net": {"name": "datatables.net", "severity": "critical"}'],
            'nonArrayViaKey' => ['{"vulnerabilities": {"datatables.net": {"name": "datatables.net", "severity": "critical", "via": "non-array"}}}'],
        ];
    }

    /**
     * @return array<string, array<string>>
     */
    public static function vulnerabilitiesThatShouldBeSkippedDataProvider(): array
    {
        return [
            'skipInfoVulnerability' => ['{"vulnerabilities": {"datatables.net": {"name": "datatables.net", "severity": "info", "via": [{"title": "Cross site scripting"}]}}}'],
            'skipModerateVulnerability' => ['{"vulnerabilities": {"datatables.net": {"name": "datatables.net", "severity": "moderate", "via": [{"title": "Cross site scripting"}]}}}'],
            'skipNoTitleVulnerability' => ['{"vulnerabilities": {"datatables.net": {"name": "datatables.net", "severity": "critical", "via": [{"url": "http://some-url"}]}}}'],
            'skipNonRootVulnerability' => ['{"vulnerabilities": {"datatables.net": {"name": "datatables.net", "severity": "critical", "via": ["@spryker/oryx","autoprefixer"]}}}'],
        ];
    }

    /**
     * @param string $stdOut
     * @param string $stdErr
     * @param bool $isSuccessful
     *
     * @return \Symfony\Component\Process\Process
     */
    public function createProcessMock(string $stdOut, string $stdErr = '', bool $isSuccessful = false): Process
    {
        $process = $this->createMock(Process::class);
        $process->method('isSuccessful')->willReturn($isSuccessful);
        $process->method('getOutput')->willReturn($stdOut);
        $process->method('getErrorOutput')->willReturn($stdErr);

        return $process;
    }

    /**
     * @param \Symfony\Component\Process\Process $process
     *
     * @return \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    public function createProcessRunnerMock(Process $process): ProcessRunnerServiceInterface
    {
        $processRunner = $this->createMock(ProcessRunnerServiceInterface::class);
        $processRunner->method('run')->willReturn($process);

        return $processRunner;
    }
}
