<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Acceptance\Checker;

use SprykerSdk\Evaluator\Checker\DeadCode\DeadCodeChecker;
use SprykerSdkTest\Evaluator\Acceptance\ApplicationTestCase;
use SprykerSdkTest\Evaluator\Acceptance\TestHelper;
use Symfony\Component\Console\Command\Command;

/**
 * @group SprykerSdkTest
 * @group Evaluator
 * @group Acceptance
 * @group Checker
 * @group DeadCodeCheckerTest
 */
class DeadCodeCheckerTest extends ApplicationTestCase
{
    /**
     * @return void
     */
    public function testReturnSuccessOnValidProject(): void
    {
        $commandTester = $this->createCommandTester(TestHelper::VALID_PROJECT_PATH);
        $commandTester->execute([
            // pass arguments to the helper
            '--checkers' => DeadCodeChecker::NAME,
            '--format' => 'json',
        ]);

        $commandTester->assertCommandIsSuccessful();
        $this->assertSame(
            '[]',
            $commandTester->getDisplay(),
        );
    }

    /**
     * @return void
     */
    public function testReturnViolationWhenProjectHasIssues(): void
    {
        $commandTester = $this->createCommandTester(TestHelper::INVALID_PROJECT_PATH);
        $commandTester->execute([
            // pass arguments to the helper
            '--checkers' => DeadCodeChecker::NAME,
            '--format' => 'json',
        ]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertStringContainsString(
            'https:\/\/docs.spryker.com\/docs\/scos\/dev\/guidelines\/keeping-a-project-upgradable\/upgradability-guidelines\/dead-code-checker.html',
            $commandTester->getDisplay(),
            'The output must contain correct link.',
        );
    }
}
