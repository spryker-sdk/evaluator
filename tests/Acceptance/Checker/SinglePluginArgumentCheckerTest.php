<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Acceptance\Checker;

use SprykerSdk\Evaluator\Checker\SinglePluginArgumentChecker\SinglePluginArgumentChecker;
use SprykerSdkTest\Evaluator\Acceptance\ApplicationTestCase;
use SprykerSdkTest\Evaluator\Acceptance\TestHelper;
use Symfony\Component\Console\Command\Command;

/**
 * @group SprykerSdkTest
 * @group Evaluator
 * @group Acceptance
 * @group Checker
 * @group SinglePluginArgumentCheckerTest
 */
class SinglePluginArgumentCheckerTest extends ApplicationTestCase
{
    /**
     * @return void
     */
    public function testReturnSuccessOnValidProject(): void
    {
        $commandTester = $this->createCommandTester(TestHelper::VALID_PROJECT_PATH);
        $commandTester->execute(['--checkers' => SinglePluginArgumentChecker::NAME, '--path' => 'src/Pyz/Zed/SinglePluginArgument']);

        $commandTester->assertCommandIsSuccessful();
    }

    /**
     * @return void
     */
    public function testReturnViolationWhenProjectHasIssues(): void
    {
        $commandTester = $this->createCommandTester(TestHelper::INVALID_PROJECT_PATH);
        $commandTester->execute([
            '--checkers' => SinglePluginArgumentChecker::NAME,
            '--path' => 'src/Pyz/Zed/SinglePluginArgument',
            '--format' => 'json',
        ]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertJson($commandTester->getDisplay());
        $this->assertSame(
            '{"SINGLE_PLUGIN_ARGUMENT":{"docUrl":"https:\/\/docs.spryker.com\/docs\/scos\/dev\/guidelines\/keeping-a-project-upgradable\/upgradability-guidelines\/single-plugin-argument.html","violations":[{"message":"Plugin Spryker\\\\Zed\\\\Monitoring\\\\Communication\\\\Plugin\\\\Console\\\\MonitoringConsolePlugin has unsupported constructor parameters.\nSupported argument types: int, float, string, const, bool, usage of new statement to\ninstantiate a class (without further methods calls) ","target":"SprykerSdkTest\\\\InvalidProject\\\\Pyz\\\\Zed\\\\SinglePluginArgument\\\\ConsoleDependencyProvider::getMonitoringConsoleMethod"}]}}',
            $commandTester->getDisplay(),
        );
    }
}
