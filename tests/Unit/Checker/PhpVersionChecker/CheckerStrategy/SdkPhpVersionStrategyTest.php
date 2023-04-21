<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Checker\PhpVersionChecker\CheckerStrategy;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy\SdkPhpVersionStrategy;

class SdkPhpVersionStrategyTest extends TestCase
{
    /**
     * @return void
     */
    public function testCheckShouldReturnViolationWhenSdkPhpVersionIsNotAllowed(): void
    {
        //Arrange
        $checkerStrategy = new SdkPhpVersionStrategy(['7.4']);

        //Act
        $response = $checkerStrategy->check(['8.0']);

        //Assert
        $this->assertEmpty($response->getUsedVersions());
        $this->assertCount(1, $response->getViolations());
        $this->assertStringMatchesFormat(SdkPhpVersionStrategy::MESSAGE_INVALID_PHP_VERSION, $response->getViolations()[0]->getMessage());
    }

    /**
     * @return void
     */
    public function testCheckShouldReturnAllowedPhpVersions(): void
    {
        //Arrange
        $sdkPhpVersionStrategy = new SdkPhpVersionStrategy(['7.4']);

        //Act
        $response = $sdkPhpVersionStrategy->check(['7.4', '8.0']);

        //Assert
        $this->assertEmpty($response->getViolations());
        $this->assertSame(['7.4'], $response->getUsedVersions());
    }
}
