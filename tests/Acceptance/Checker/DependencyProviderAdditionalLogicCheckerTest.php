<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Acceptance\Checker;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Checker\DependencyProviderAdditionalLogicChecker\DependencyProviderAdditionalLogicChecker;
use SprykerSdkTest\Evaluator\Acceptance\TestHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Process\Process;

/**
 * @group SprykerSdkTest
 * @group Evaluator
 * @group Acceptance
 * @group Checker
 * @group DependencyProviderAdditionalLogicCheckerTest
 */
class DependencyProviderAdditionalLogicCheckerTest extends TestCase
{
    /**
     * @return void
     */
    public function testReturnSuccessOnValidProject(): void
    {
        $process = new Process(
            ['bin/console', 'evaluator:run', '--checkers', DependencyProviderAdditionalLogicChecker::NAME],
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
            ['bin/console', 'evaluator:run', '--checkers', DependencyProviderAdditionalLogicChecker::NAME],
            null,
            ['EVALUATOR_PROJECT_DIR' => TestHelper::INVALID_PROJECT_PATH],
        );
        $process->run();

        $this->assertSame(Command::FAILURE, $process->getExitCode());
        $this->assertSame(
            <<<OUT
        ============================================
        DEPENDENCY PROVIDER ADDITIONAL LOGIC CHECKER
        ============================================

        +---+----------------------------------------------------------------------------------------+--------------------------------------------------------------------------------------------------------------------------+
        | # | Message                                                                                | Target                                                                                                                   |
        +---+----------------------------------------------------------------------------------------+--------------------------------------------------------------------------------------------------------------------------+
        | 1 | The condition statement if (!static::IS_DEV) {} is forbidden in the DependencyProvider | tests/Acceptance/_data/InvalidProject/src/Pyz/Zed/DependencyProviderAdditionalLogicChecker/ConsoleDependencyProvider.php |
        +---+----------------------------------------------------------------------------------------+--------------------------------------------------------------------------------------------------------------------------+


        OUT,
            $process->getOutput(),
        );
    }
}
