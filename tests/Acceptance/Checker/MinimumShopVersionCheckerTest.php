<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Acceptance\Checker;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Checker\MinimumShopVersionChecker\MinimumShopVersionChecker;
use SprykerSdk\Evaluator\Console\Command\EvaluatorCommand;
use SprykerSdkTest\Evaluator\Acceptance\TestHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Process\Process;

/**
 * @group SprykerSdkTest
 * @group Evaluator
 * @group Acceptance
 * @group Checker
 * @group MinimumShopVersionCheckerTest
 */
class MinimumShopVersionCheckerTest extends TestCase
{
    /**
     * @return void
     */
    public function testReturnSuccessOnValidProject(): void
    {
        $process = new Process(
            ['bin/console', EvaluatorCommand::COMMAND_NAME, '--checkers', MinimumShopVersionChecker::NAME],
            null,
            ['EVALUATOR_PROJECT_DIR' => TestHelper::VALID_PROJECT_PATH],
        );
        $process->run();

        $this->assertSame(Command::SUCCESS, $process->getExitCode());
    }

    /**
     * @return void
     */
    public function testReturnViolationWhenProjectHasIssues(): void
    {
        $process = new Process(
            ['bin/console', EvaluatorCommand::COMMAND_NAME, '--checkers', MinimumShopVersionChecker::NAME],
            null,
            ['EVALUATOR_PROJECT_DIR' => TestHelper::INVALID_PROJECT_PATH],
        );
        $process->run();

        $this->assertSame(
            <<<OUT
        ============================
        MINIMUM ALLOWED SHOP VERSION
        ============================

        +---+-------------------------------------------------------------------------------------------------------------------+---------------------------------------+
        | # | Message                                                                                                           | Target                                |
        +---+-------------------------------------------------------------------------------------------------------------------+---------------------------------------+
        | 1 | Package "spryker-feature/agent-assist" version "202203.0" is not supported. Minimum allowed version is "202204.0" | spryker-feature/agent-assist:202203.0 |
        +---+-------------------------------------------------------------------------------------------------------------------+---------------------------------------+
        | 2 | Package "spryker/availability-gui" version "6.5.9" is not supported. Minimum allowed version is "6.6.0"           | spryker/availability-gui:6.5.9        |
        +---+-------------------------------------------------------------------------------------------------------------------+---------------------------------------+

        Read more: https://docs.spryker.com/docs/scos/dev/keeping-a-project-upgradable/upgradability-guidelines/minimum-allowed-shop-version.html


        OUT,
            $process->getOutput(),
        );

        $this->assertSame(Command::FAILURE, $process->getExitCode());
    }
}
