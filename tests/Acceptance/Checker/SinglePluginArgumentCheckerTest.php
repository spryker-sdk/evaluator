<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Acceptance\Checker;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Checker\SinglePluginArgumentChecker\SinglePluginArgumentChecker;
use SprykerSdk\Evaluator\Console\Command\EvaluatorCommand;
use SprykerSdkTest\Evaluator\Acceptance\TestHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Process\Process;

/**
 * @group SprykerSdkTest
 * @group Evaluator
 * @group Acceptance
 * @group Checker
 * @group SinglePluginArgumentCheckerTest
 */
class SinglePluginArgumentCheckerTest extends TestCase
{
    /**
     * @return void
     */
    public function testReturnSuccessOnValidProject(): void
    {
        $process = new Process(
            ['bin/console', EvaluatorCommand::COMMAND_NAME, '--checkers', SinglePluginArgumentChecker::NAME],
            null,
            ['EVALUATOR_PROJECT_DIR' => TestHelper::VALID_PROJECT_PATH . '/src/Pyz/Zed/SinglePluginArgument'],
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
            ['bin/console', EvaluatorCommand::COMMAND_NAME, '--checkers', SinglePluginArgumentChecker::NAME, '--format', 'json'],
            null,
            ['EVALUATOR_PROJECT_DIR' => TestHelper::INVALID_PROJECT_PATH . '/src/Pyz/Zed/SinglePluginArgument'],
        );
        $process->run();

        $this->assertSame(Command::FAILURE, $process->getExitCode());
        $this->assertJsonStringEqualsJsonString(
            '{"SINGLE_PLUGIN_ARGUMENT":{"docUrl":"https:\/\/docs.spryker.com\/docs\/scos\/dev\/keeping-a-project-upgradable\/upgradability-guidelines\/single-plugin-argument.html","violations":[{"message":"Plugin Spryker\\\\Zed\\\\Monitoring\\\\Communication\\\\Plugin\\\\Console\\\\MonitoringConsolePlugin has unsupported constructor parameters.\nSupported argument types: int, float, string, const, bool, usage of new statement to\ninstantiate a class (without further methods calls) ","target":"SprykerSdkTest\\\\InvalidProject\\\\Pyz\\\\Zed\\\\SinglePluginArgument\\\\ConsoleDependencyProvider::getMonitoringConsoleMethod"}]}}',
            $process->getOutput(),
        );
    }
}
