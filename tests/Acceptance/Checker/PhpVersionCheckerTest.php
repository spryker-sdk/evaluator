<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Acceptance\Checker;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Console\Command\EvaluatorCommand;
use SprykerSdkTest\Evaluator\Acceptance\TestHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Process\Process;

/**
 * @group SprykerSdkTest
 * @group Evaluator
 * @group Acceptance
 * @group Checker
 * @group PhpVersionCheckerTest
 */
class PhpVersionCheckerTest extends TestCase
{
    /**
     * @return void
     */
    public function testReturnSuccessOnValidProject(): void
    {
        $process = new Process(
            ['bin/console', EvaluatorCommand::COMMAND_NAME, '--checkers', 'PHP_VERSION_CHECKER'],
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
            ['bin/console', EvaluatorCommand::COMMAND_NAME, '--checkers', 'PHP_VERSION_CHECKER'],
            null,
            ['EVALUATOR_PROJECT_DIR' => TestHelper::INVALID_PROJECT_PATH],
        );
        $process->run();

        $phpVersion = PHP_VERSION;

        $this->assertSame(Command::FAILURE, $process->getExitCode());
        $this->assertSame(
            <<<OUT
        ===================
        PHP VERSION CHECKER
        ===================

        +---+-----------------------------------------------------------------------------+--------------------------------------------------------+
        | # | Message                                                                     | Target                                                 |
        +---+-----------------------------------------------------------------------------+--------------------------------------------------------+
        | 1 | Composer json PHP constraint ">=8.1" does not match allowed php versions    | tests/Acceptance/_data/InvalidProject/composer.json    |
        +---+-----------------------------------------------------------------------------+--------------------------------------------------------+
        | 2 | Deploy file uses not allowed PHP image version "spryker/php:7.2-alpine3.12" | tests/Acceptance/_data/InvalidProject/deploy.yml       |
        |   | Image tag must contain allowed PHP version (image:abc-8.0)                  |                                                        |
        +---+-----------------------------------------------------------------------------+--------------------------------------------------------+
        | 3 | Not all the targets have same PHP versions                                  | Current php version $phpVersion: php7.4                     |
        |   |                                                                             | tests/Acceptance/_data/InvalidProject/composer.json: - |
        |   |                                                                             | tests/Acceptance/_data/InvalidProject/deploy**.yml: -  |
        |   |                                                                             | SDK php versions: php7.4, php8.0                       |
        +---+-----------------------------------------------------------------------------+--------------------------------------------------------+

        Read more: https://docs.spryker.com/docs/scos/dev/keeping-a-project-upgradable/upgradability-guidelines/php-version.html


        OUT,
            $process->getOutput(),
        );
    }
}
