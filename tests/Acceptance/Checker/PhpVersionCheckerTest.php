<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Acceptance\Checker;

use PHPUnit\Framework\TestCase;
use SprykerSdkTest\Evaluator\Acceptance\TestHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Process\Process;

class PhpVersionCheckerTest extends TestCase
{
    /**
     * @return void
     */
    public function testReturnSuccessOnValidProject(): void
    {
        $process = new Process(['bin/console', 'evaluator:run', '--path', TestHelper::VALID_PROJECT_PATH, '--checkers', 'PHP_VERSION_CHECKER']);
        $process->run();

        $this->assertSame(Command::SUCCESS, $process->getExitCode());
    }

    /**
     * @return void
     */
    public function testReturnViolationWhenProjectHasIssues(): void
    {
        $process = new Process(['bin/console', 'evaluator:run', '--path', TestHelper::INVALID_PROJECT_PATH, '--checkers', 'PHP_VERSION_CHECKER']);
        $process->run();

        $this->assertSame(Command::FAILURE, $process->getExitCode());
    }
}
