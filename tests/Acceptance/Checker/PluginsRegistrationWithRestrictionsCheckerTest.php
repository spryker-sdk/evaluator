<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Acceptance\Checker;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Checker\PluginsRegistrationWithRestrictionsChecker\PluginsRegistrationWithRestrictionsChecker;
use SprykerSdk\Evaluator\Console\Command\EvaluatorCommand;
use SprykerSdkTest\Evaluator\Acceptance\TestHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Process\Process;

/**
 * @group SprykerSdkTest
 * @group Evaluator
 * @group Acceptance
 * @group Checker
 * @group PluginsRegistrationWithRestrictionsCheckerTest
 */
class PluginsRegistrationWithRestrictionsCheckerTest extends TestCase
{
    /**
     * @return void
     */
    public function testReturnSuccessOnValidProject(): void
    {
        $process = new Process(
            [
                'bin/console',
                EvaluatorCommand::COMMAND_NAME,
                '--path',
                'src/Pyz/Zed/PluginsRegistrationWithRestrictionsChecker',
                '--checkers',
                PluginsRegistrationWithRestrictionsChecker::NAME,
            ],
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
            [
                'bin/console',
                EvaluatorCommand::COMMAND_NAME,
                '--path',
                'src/Pyz/Zed/PluginsRegistrationWithRestrictionsChecker',
                '--checkers',
                PluginsRegistrationWithRestrictionsChecker::NAME,
            ],
            null,
            ['EVALUATOR_PROJECT_DIR' => TestHelper::INVALID_PROJECT_PATH],
        );
        $process->run();

        $this->assertSame(Command::FAILURE, $process->getExitCode());

        $this->assertSame(
            <<<OUT
        ==============================================
        PLUGINS REGISTRATION WITH RESTRICTIONS CHECKER
        ==============================================

        +---+--------------------------------------------------------------------------------------------------------------------------------+-----------------------------------+
        | # | Message                                                                                                                        | Target                            |
        +---+--------------------------------------------------------------------------------------------------------------------------------+-----------------------------------+
        | 1 | Class "\Spryker\Zed\CategoryNavigationConnector\Communication\Plugin\InvalidPlagin" is not used in current dependency provider | CategoryDependencyProvider.php:25 |
        +---+--------------------------------------------------------------------------------------------------------------------------------+-----------------------------------+
        | 2 | Restriction rule does not match the pattern "/^\* - (before|after) \{@link (?<class>.+)\}( .*\.|)$/"                           | CategoryDependencyProvider.php:25 |
        +---+--------------------------------------------------------------------------------------------------------------------------------+-----------------------------------+
        | 3 | Restriction rule does not match the pattern "/^\* - (before|after) \{@link (?<class>.+)\}( .*\.|)$/"                           | CategoryDependencyProvider.php:47 |
        +---+--------------------------------------------------------------------------------------------------------------------------------+-----------------------------------+

        Read more: https://docs.spryker.com/docs/scos/dev/keeping-a-project-upgradable/upgradability-guidelines/plugin-registration-with-restrintions.html


        OUT,
            $process->getOutput(),
        );
    }
}
