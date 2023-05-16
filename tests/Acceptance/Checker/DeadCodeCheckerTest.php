<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Acceptance\Checker;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Checker\DeadCode\DeadCodeChecker;
use SprykerSdk\Evaluator\Console\Command\EvaluatorCommand;
use SprykerSdkTest\Evaluator\Acceptance\TestHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Process\Process;

/**
 * @group SprykerSdkTest
 * @group Evaluator
 * @group Acceptance
 * @group Checker
 * @group DeadCodeCheckerTest
 */
class DeadCodeCheckerTest extends TestCase
{
    /**
     * @return void
     */
    public function testReturnSuccessOnValidProject(): void
    {
        $process = new Process(
            ['bin/console', EvaluatorCommand::COMMAND_NAME, '--checkers', DeadCodeChecker::NAME, '--format', 'json'],
            null,
            ['EVALUATOR_PROJECT_DIR' => TestHelper::VALID_PROJECT_PATH],
        );
        $process->run();

        $this->assertSame(Command::SUCCESS, $process->getExitCode());
        $this->assertSame(
            '[]',
            $process->getOutput(),
        );
    }

    /**
     * @return void
     */
    public function testReturnViolationWhenProjectHasIssues(): void
    {
        $process = new Process(
            ['bin/console', EvaluatorCommand::COMMAND_NAME, '--checkers', DeadCodeChecker::NAME, '--format', 'json'],
            null,
            ['EVALUATOR_PROJECT_DIR' => TestHelper::INVALID_PROJECT_PATH],
        );
        $process->run();

        $this->assertSame(Command::FAILURE, $process->getExitCode());
        $this->assertJson($process->getOutput());
    }
}
