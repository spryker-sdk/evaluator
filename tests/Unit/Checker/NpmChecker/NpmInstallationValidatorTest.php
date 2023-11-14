<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Checker\NpmChecker;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Checker\NpmChecker\NpmInstallationValidator;
use SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface;
use Symfony\Component\Process\Process;

class NpmInstallationValidatorTest extends TestCase
{
    /**
     * @return void
     */
    public function testIsNpmInstalledShouldReturnCommandResult(): void
    {
        //Arrange
        $validator = new NpmInstallationValidator($this->createProcessRunnerMock());

        //Act
        $result = $validator->isNpmInstalled();

        //Assert
        $this->assertTrue($result);
    }

    /**
     * @param bool $isCommandSuccessful
     *
     * @return \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    public function createProcessRunnerMock(bool $isCommandSuccessful = true): ProcessRunnerServiceInterface
    {
        $processRunner = $this->createMock(ProcessRunnerServiceInterface::class);
        $process = $this->createMock(Process::class);

        $process->method('isSuccessful')->willReturn($isCommandSuccessful);
        $processRunner->method('run')->willReturn($process);

        return $processRunner;
    }
}
