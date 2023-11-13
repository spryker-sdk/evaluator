<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Checker\DiscouragedPackagesChecker;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Checker\DiscouragedPackagesChecker\DiscouragedPackageDto;
use SprykerSdk\Evaluator\Checker\DiscouragedPackagesChecker\DiscouragedPackagesChecker;
use SprykerSdk\Evaluator\Checker\DiscouragedPackagesChecker\DiscouragedPackagesFetcherInterface;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Reader\ComposerReaderInterface;

class DiscouragedPackagesCheckerTest extends TestCase
{
    /**
     * @return void
     */
    public function testCheckShouldReturnViolationWithInternalError(): void
    {
        // Arrange
        $installedPackages = ['aaa/bbb' => ['name' => 'aaa/bbb'], 'zzz/xxx' => ['name' => 'zzz/xxx']];
        $exceptionMessage = 'Invalid response data';

        $discouragedPackagesChecker = new DiscouragedPackagesChecker(
            $this->createDiscouragedPackagesFetcherMock([], $exceptionMessage),
            $this->createComposerReaderMock($installedPackages),
        );

        // Act
        $response = $discouragedPackagesChecker->check(new CheckerInputDataDto('', []));

        // Assert
        $this->assertCount(1, $response->getViolations());
        $this->assertStringContainsString($exceptionMessage, $response->getViolations()[0]->getMessage());
    }

    /**
     * @return void
     */
    public function testCheckShouldReturnViolationWithDiscouragedPackages(): void
    {
        // Arrange
        $installedPackages = ['aaa/bbb' => ['name' => 'aaa/bbb'], 'zzz/xxx' => ['name' => 'zzz/xxx']];

        $discouragedPackagesChecker = new DiscouragedPackagesChecker(
            $this->createDiscouragedPackagesFetcherMock([new DiscouragedPackageDto('zzz/xxx', 'Deprecated package')]),
            $this->createComposerReaderMock($installedPackages),
        );

        // Act
        $response = $discouragedPackagesChecker->check(new CheckerInputDataDto('', []));

        // Assert
        $this->assertCount(1, $response->getViolations());
        $this->assertStringContainsString('zzz/xxx', $response->getViolations()[0]->getTarget());
    }

    /**
     * @return void
     */
    public function testGetNameShouldReturnCheckerName(): void
    {
        // Arrange
        $discouragedPackagesChecker = new DiscouragedPackagesChecker(
            $this->createDiscouragedPackagesFetcherMock([]),
            $this->createComposerReaderMock([]),
        );

        // Act
        $name = $discouragedPackagesChecker->getName();

        // Assert
        $this->assertSame(DiscouragedPackagesChecker::NAME, $name);
    }

    /**
     * @param array<\SprykerSdk\Evaluator\Checker\DiscouragedPackagesChecker\DiscouragedPackageDto> $discouragedPackageDtos
     * @param string $throwExceptionMessage
     *
     * @return \SprykerSdk\Evaluator\Checker\DiscouragedPackagesChecker\DiscouragedPackagesFetcherInterface
     */
    protected function createDiscouragedPackagesFetcherMock(
        array $discouragedPackageDtos,
        string $throwExceptionMessage = ''
    ): DiscouragedPackagesFetcherInterface {
        $discouragedPackagesFetcher = $this->createMock(DiscouragedPackagesFetcherInterface::class);

        if ($throwExceptionMessage) {
            $discouragedPackagesFetcher
                ->method('fetchDiscouragedPackagesByPackageNames')
                ->willThrowException(new InvalidArgumentException($throwExceptionMessage));
        }

        $discouragedPackagesFetcher
            ->method('fetchDiscouragedPackagesByPackageNames')
            ->willReturn($discouragedPackageDtos);

        return $discouragedPackagesFetcher;
    }

    /**
     * @param array<string, array<mixed>> $installedPackages
     *
     * @return \SprykerSdk\Evaluator\Reader\ComposerReaderInterface
     */
    protected function createComposerReaderMock(array $installedPackages): ComposerReaderInterface
    {
        $composerReader = $this->createMock(ComposerReaderInterface::class);
        $composerReader->method('getInstalledPackages')->willReturn($installedPackages);

        return $composerReader;
    }
}
