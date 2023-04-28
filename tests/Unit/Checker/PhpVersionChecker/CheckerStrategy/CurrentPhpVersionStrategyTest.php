<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Checker\PhpVersionChecker\CheckerStrategy;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy\CurrentPhpVersionStrategy;

class CurrentPhpVersionStrategyTest extends TestCase
{
    /**
     * @return void
     */
    public function testCheckShouldReturnViolationWhenSdkPhpVersionIsNotAllowed(): void
    {
        //Arrange
        $checkerStrategy = new CurrentPhpVersionStrategy();

        //Act
        $response = $checkerStrategy->check(['5.0'], '');

        //Assert
        $this->assertEmpty($response->getUsedVersions());
        $this->assertCount(1, $response->getViolations());
        $this->assertStringMatchesFormat(CurrentPhpVersionStrategy::MESSAGE_INVALID_LOCAL_PHO_VERSION, $response->getViolations()[0]->getMessage());
    }

    /**
     * @return void
     */
    public function testCheckShouldReturnAllowedPhpVersions(): void
    {
        //Arrange
        $checkerStrategy = new CurrentPhpVersionStrategy();

        //Act
        $response = $checkerStrategy->check([PHP_VERSION], '');

        //Assert
        $this->assertEmpty($response->getViolations());
        $this->assertSame([PHP_VERSION], $response->getUsedVersions());
    }
}
