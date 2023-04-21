<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Checker\PhpVersionChecker\CheckerStrategy;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy\DeployYamlFilesPhpVersionStrategy;
use SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy\FileReader\DeploymentYamlFileReader;

class DeployYamlFilesPhpVersionStrategyTest extends TestCase
{
    /**
     * @return void
     */
    public function testCheckShouldReturnViolationWhenImageTagNotFound(): void
    {
        //Arrange
        $deploymentFileReaderMock = $this->createDeploymentYamlFileReaderMock(['deploy.yml' => []]);

        $checkerStrategy = new DeployYamlFilesPhpVersionStrategy($deploymentFileReaderMock);

        //Act
        $response = $checkerStrategy->check(['7.4', '8.0'], '');

        //Assert
        $this->assertEmpty($response->getUsedVersions());
        $this->assertCount(1, $response->getViolations());
        $this->assertStringMatchesFormat(DeployYamlFilesPhpVersionStrategy::MESSAGE_YAML_PATH_NOT_FOUND, $response->getViolations()[0]->getMessage());
    }

    /**
     * @return void
     */
    public function testCheckShouldReturnSuccessWhenDeployFilesNotFound(): void
    {
        //Arrange
        $deploymentFileReaderMock = $this->createDeploymentYamlFileReaderMock([]);

        $checkerStrategy = new DeployYamlFilesPhpVersionStrategy($deploymentFileReaderMock);

        //Act
        $response = $checkerStrategy->check(['7.4', '8.0'], '');

        //Assert
        $this->assertSame(['7.4', '8.0'], $response->getUsedVersions());
        $this->assertCount(0, $response->getViolations());
    }

    /**
     * @return void
     */
    public function testCheckShouldReturnViolationWhenDeployFilesUseNotAllowedPhpVersion(): void
    {
        //Arrange
        $deploymentFileReaderMock = $this->createDeploymentYamlFileReaderMock(['deploy.yaml' => ['image' => ['tag' => 'spryker/php:8.1-alpine3.12']]]);

        $checkerStrategy = new DeployYamlFilesPhpVersionStrategy($deploymentFileReaderMock);

        //Act
        $response = $checkerStrategy->check(['7.4', '8.0'], '');

        //Assert
        $this->assertEmpty($response->getUsedVersions());
        $this->assertCount(1, $response->getViolations());
        $this->assertStringMatchesFormat(DeployYamlFilesPhpVersionStrategy::MESSAGE_USED_NOT_ALLOWED_PHP_VERSION, $response->getViolations()[0]->getMessage());
    }

    /**
     * @return void
     */
    public function testCheckShouldReturnSuccessWhenDeployFilesUseAllowedPhpVersion(): void
    {
        //Arrange
        $deploymentFileReaderMock = $this->createDeploymentYamlFileReaderMock(['deploy.yaml' => ['image' => ['tag' => 'spryker/php:8.0-alpine3.12']]]);

        $checkerStrategy = new DeployYamlFilesPhpVersionStrategy($deploymentFileReaderMock);

        //Act
        $response = $checkerStrategy->check(['7.4', '8.0'], '');

        //Assert
        $this->assertSame(['8.0'], array_values($response->getUsedVersions()));
        $this->assertCount(0, $response->getViolations());
    }

    /**
     * @param array<mixed> $deployFileContent
     *
     * @return \SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy\FileReader\DeploymentYamlFileReader
     */
    protected function createDeploymentYamlFileReaderMock(array $deployFileContent): DeploymentYamlFileReader
    {
        $deploymentYamlFileReader = $this->createMock(DeploymentYamlFileReader::class);
        $deploymentYamlFileReader->method('read')->willReturn($deployFileContent);

        return $deploymentYamlFileReader;
    }
}
