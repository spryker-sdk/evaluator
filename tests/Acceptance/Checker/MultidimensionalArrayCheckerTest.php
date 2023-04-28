<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Acceptance\Checker;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Checker\MultidimensionalArrayChecker\MultidimensionalArrayChecker;
use SprykerSdk\Evaluator\Console\Command\EvaluatorCommand;
use SprykerSdkTest\Evaluator\Acceptance\TestHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Process\Process;

/**
 * @group SprykerSdkTest
 * @group Evaluator
 * @group Acceptance
 * @group Checker
 * @group MultidimensionalArrayCheckerTest
 */
class MultidimensionalArrayCheckerTest extends TestCase
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
                TestHelper::VALID_PROJECT_PATH,
                '--checkers',
                MultidimensionalArrayChecker::NAME,
            ],
        );
        $process->run();

        $this->assertSame(Command::SUCCESS, $process->getExitCode());
    }

    /**
     * @dataProvider differentIssueCases
     *
     * @param string $path
     *
     * @return void
     */
    public function testReturnViolationWhenProjectHasIssues(string $path): void
    {
        $process = new Process(
            [
                'bin/console',
                EvaluatorCommand::COMMAND_NAME,
                '--path',
                $path,
                '--checkers',
                MultidimensionalArrayChecker::NAME,
            ],
        );
        $process->run();

        $this->assertSame(Command::FAILURE, $process->getExitCode());
    }

    /**
     * @return array<string, array<string>>
     */
    public function differentIssueCases(): array
    {
        return [
            'return multidimensional array' => [TestHelper::INVALID_PROJECT_PATH . '/src/Pyz/MultidimensionalArray/ReturnArray'],
            'assign multidimensional array' => [TestHelper::INVALID_PROJECT_PATH . '/src/Pyz/MultidimensionalArray/AssignArray'],
            'merge multidimensional array' => [TestHelper::INVALID_PROJECT_PATH . '/src/Pyz/MultidimensionalArray/ArrayMerge'],
        ];
    }
}
