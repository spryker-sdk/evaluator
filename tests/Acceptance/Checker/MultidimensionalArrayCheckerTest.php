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
                '--checkers',
                MultidimensionalArrayChecker::NAME,
                '--format',
                'json',
            ],
            null,
            ['EVALUATOR_PROJECT_DIR' => TestHelper::VALID_PROJECT_PATH],
        );
        $process->run();

        $this->assertSame(Command::SUCCESS, $process->getExitCode());
        $this->assertSame('[]', $process->getOutput());
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
            null,
            ['EVALUATOR_PROJECT_DIR' => TestHelper::INVALID_PROJECT_PATH],
        );
        $process->run();

        $this->assertSame(Command::FAILURE, $process->getExitCode());

        $this->assertStringContainsString(
            'https://docs.spryker.com/docs/scos/dev/guidelines/keeping-a-project-upgradable/upgradability-guidelines/multidimensional-array.html',
            $process->getOutput(),
            'The output must contain correct link.',
        );
    }

    /**
     * @return array<string, array<string>>
     */
    public function differentIssueCases(): array
    {
        return [
            'return multidimensional array' => ['src/Pyz/Zed/MultidimensionalArray/ReturnArray'],
            'assign multidimensional array' => ['src/Pyz/Zed/MultidimensionalArray/AssignArray'],
            'merge multidimensional array' => ['src/Pyz/Zed/MultidimensionalArray/ArrayMerge'],
        ];
    }
}
