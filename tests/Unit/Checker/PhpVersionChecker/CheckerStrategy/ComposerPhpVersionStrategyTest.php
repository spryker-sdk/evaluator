<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Checker\PhpVersionChecker\CheckerStrategy;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy\ComposerPhpVersionStrategy;
use SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy\FileReader\ComposerFileReader;

class ComposerPhpVersionStrategyTest extends TestCase
{
    /**
     * @return void
     */
    public function testCheckShouldReturnViolationWhenReaderFailedWithException(): void
    {
        //Arrange
        $composerFileReaderMock = $this->createMock(ComposerFileReader::class);
        $composerFileReaderMock->method('read')->willThrowException(new InvalidArgumentException('can\'t read the file'));

        $checkerStrategy = new ComposerPhpVersionStrategy($composerFileReaderMock);

        //Act
        $response = $checkerStrategy->check(['7.4', '8.0'], '');

        //Assert
        $this->assertEmpty($response->getUsedVersions());
        $this->assertCount(1, $response->getViolations());
        $this->assertSame('can\'t read the file', $response->getViolations()[0]->getMessage());
    }

    /**
     * @return void
     */
    public function testCheckShouldReturnNoPhpDependencyViolationWhenComposerJsonHasNoPhpVersion(): void
    {
        //Arrange
        $composerFileReaderMock = $this->createComposerFileReaderMock(['require' => ['spryker-eco/loggly' => '^0.1.1']]);

        $checkerStrategy = new ComposerPhpVersionStrategy($composerFileReaderMock);

        //Act
        $response = $checkerStrategy->check(['7.4', '8.0'], '');

        //Assert
        $this->assertEmpty($response->getUsedVersions());
        $this->assertCount(1, $response->getViolations());
        $this->assertStringMatchesFormat(ComposerPhpVersionStrategy::MESSAGE_NO_PHP_DEPENDENCY, $response->getViolations()[0]->getMessage());
    }

    /**
     * @return void
     */
    public function testCheckShouldReturnNotAllowedPhpVersionViolationWhenComposerHasInvalidPhpVersion(): void
    {
        //Arrange
        $composerFileReaderMock = $this->createComposerFileReaderMock(['require' => ['php' => '>=8.1', 'spryker-eco/loggly' => '^0.1.1']]);

        $checkerStrategy = new ComposerPhpVersionStrategy($composerFileReaderMock);

        //Act
        $response = $checkerStrategy->check(['7.4', '8.0'], '');

        //Assert
        $this->assertEmpty($response->getUsedVersions());
        $this->assertCount(1, $response->getViolations());
        $this->assertStringMatchesFormat(ComposerPhpVersionStrategy::MESSAGE_USED_NOT_ALLOWED_PHP_VERSION, $response->getViolations()[0]->getMessage());
    }

    /**
     * @return void
     */
    public function testCheckShouldReturnSuccessResponseWhenValidPhpVersionUsed(): void
    {
        //Arrange
        $composerFileReaderMock = $this->createComposerFileReaderMock(['require' => ['php' => '>=7.4', 'spryker-eco/loggly' => '^0.1.1']]);

        $checkerStrategy = new ComposerPhpVersionStrategy($composerFileReaderMock);

        //Act
        $response = $checkerStrategy->check(['7.4', '8.0'], '');

        //Assert
        $this->assertSame(['7.4', '8.0'], $response->getUsedVersions());
        $this->assertCount(0, $response->getViolations());
    }

    /**
     * @param array<mixed> $composerJson
     *
     * @return \SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy\FileReader\ComposerFileReader
     */
    protected function createComposerFileReaderMock(array $composerJson): ComposerFileReader
    {
        $composerFileReader = $this->createMock(ComposerFileReader::class);
        $composerFileReader->method('read')->willReturn($composerJson);

        return $composerFileReader;
    }
}
