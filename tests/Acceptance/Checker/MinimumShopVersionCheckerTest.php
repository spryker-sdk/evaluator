<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Acceptance\Checker;

use SprykerSdk\Evaluator\Checker\MinimumShopVersionChecker\MinimumShopVersionChecker;
use SprykerSdkTest\Evaluator\Acceptance\ApplicationTestCase;
use SprykerSdkTest\Evaluator\Acceptance\TestHelper;
use Symfony\Component\Console\Command\Command;

/**
 * @group SprykerSdkTest
 * @group Evaluator
 * @group Acceptance
 * @group Checker
 * @group MinimumShopVersionCheckerTest
 */
class MinimumShopVersionCheckerTest extends ApplicationTestCase
{
    /**
     * @return void
     */
    public function testReturnSuccessOnValidProject(): void
    {
        $commandTester = $this->createCommandTester(TestHelper::VALID_PROJECT_PATH);
        $commandTester->execute(['--checkers' => MinimumShopVersionChecker::NAME]);

        $commandTester->assertCommandIsSuccessful();
    }

    /**
     * @return void
     */
    public function testReturnViolationWhenProjectHasIssues(): void
    {
        $commandTester = $this->createCommandTester(TestHelper::INVALID_PROJECT_PATH);
        $commandTester->execute(['--checkers' => MinimumShopVersionChecker::NAME]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertSame(
            <<<OUT
        ============================
        MINIMUM ALLOWED SHOP VERSION
        ============================

        +---+-----------------------------------------------------------------------------------------------------------------------+---------------------------------------+
        | # | Message                                                                                                               | Target                                |
        +---+-----------------------------------------------------------------------------------------------------------------------+---------------------------------------+
        | 1 | The package "spryker-feature/agent-assist" version 202203.0 is not supported. The minimum allowed version is 202204.0 | spryker-feature/agent-assist:202203.0 |
        +---+-----------------------------------------------------------------------------------------------------------------------+---------------------------------------+
        | 2 | The package "spryker/availability-gui" version 6.5.9 is not supported. The minimum allowed version is 6.6.0           | spryker/availability-gui:6.5.9        |
        +---+-----------------------------------------------------------------------------------------------------------------------+---------------------------------------+

        Read more: https://docs.spryker.com/docs/scos/dev/guidelines/keeping-a-project-upgradable/upgradability-guidelines/minimum-allowed-shop-version.html


        OUT,
            $commandTester->getDisplay(),
        );
    }
}
