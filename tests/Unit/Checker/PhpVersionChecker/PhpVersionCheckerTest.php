<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Checker\PhpVersionChecker;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy\PhpVersionCheckerStrategyInterface;
use SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategyResponse;
use SprykerSdk\Evaluator\Checker\PhpVersionChecker\PhpVersionChecker;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Resolver\PathResolverInterface;

class PhpVersionCheckerTest extends TestCase
{
    /**
     * @return void
     */
    public function testCheckShouldReturnViolationWhenPhpVersionsInconsistent(): void
    {
        //Arrange
        $checkerStrategyOne = $this->createPhpVersionCheckerStrategyMock(['7.4'], [], 'target one');
        $checkerStrategyTwo = $this->createPhpVersionCheckerStrategyMock(['8.0'], [], 'target two');

        $checker = new PhpVersionChecker($this->createPathResolverMock(), ['7.4', '8.0'], [$checkerStrategyOne, $checkerStrategyTwo]);

        //Act
        $violations = $checker->check(new CheckerInputDataDto(''));

        //Assert
        $this->assertCount(1, $violations);
        $this->assertStringMatchesFormat(PhpVersionChecker::MESSAGE_INCONSISTENT_PHP_VERSIONS, $violations[0]->getMessage());
    }

    /**
     * @return void
     */
    public function testCheckReturnSuccessWhenCommonPhpVersionsUsed(): void
    {
        //Arrange
        $checkerStrategyOne = $this->createPhpVersionCheckerStrategyMock(['7.4', '8.0'], [], 'target one');
        $checkerStrategyTwo = $this->createPhpVersionCheckerStrategyMock(['8.0', '8.1'], [], 'target two');

        $checker = new PhpVersionChecker($this->createPathResolverMock(), ['7.4', '8.0', '8.1'], [$checkerStrategyOne, $checkerStrategyTwo]);

        //Act
        $violations = $checker->check(new CheckerInputDataDto(''));

        //Assert
        $this->assertEmpty($violations);
    }

    /**
     * @param array<string> $usedVersions
     * @param array<\SprykerSdk\Evaluator\Dto\ViolationDto> $violations
     * @param string $target
     *
     * @return \SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy\PhpVersionCheckerStrategyInterface
     */
    protected function createPhpVersionCheckerStrategyMock(array $usedVersions, array $violations, string $target): PhpVersionCheckerStrategyInterface
    {
        $phpVersionCheckerStrategy = $this->createMock(PhpVersionCheckerStrategyInterface::class);
        $phpVersionCheckerStrategy->method('check')->willReturn(new CheckerStrategyResponse($usedVersions, $violations));
        $phpVersionCheckerStrategy->method('getTarget')->willReturn($target);

        return $phpVersionCheckerStrategy;
    }

    /**
     * @return \SprykerSdk\Evaluator\Resolver\PathResolverInterface
     */
    protected function createPathResolverMock(): PathResolverInterface
    {
        $pathResolver = $this->createMock(PathResolverInterface::class);
        $pathResolver->method('getProjectDir')->willReturn('/data');

        return $pathResolver;
    }
}
