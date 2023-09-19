<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Acceptance\ToolingSettings;

use SprykerSdk\Evaluator\Checker\DeadCode\DeadCodeChecker;
use SprykerSdk\Evaluator\Checker\MultidimensionalArrayChecker\MultidimensionalArrayChecker;
use SprykerSdk\Evaluator\Checker\PhpVersionChecker\PhpVersionChecker;
use SprykerSdkTest\Evaluator\Acceptance\ApplicationTestCase;
use Symfony\Component\Console\Command\Command;

/**
 * @group SprykerSdkTest
 * @group Evaluator
 * @group Acceptance
 * @group ToolingSettings
 * @group IgnoreErrorsTest
 */
class IgnoreErrorsTest extends ApplicationTestCase
{
    /**
     * @return void
     */
    public function testIgnoreErrorsAndReturnFailureWhenErrorsIgnorePartially(): void
    {
        $phpVersion = '6.6.6';

        $commandTester = $this->createCommandTester('tests/Acceptance/_data/IgnoreErrorsCheckProject', ['PROJECT_PHP_VERSION' => $phpVersion]);
        $commandTester->execute(['--checkers' => implode(',', [MultidimensionalArrayChecker::NAME, PhpVersionChecker::NAME])]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());

        $this->assertSame(
            <<<OUT
        ==============================
        MULTIDIMENSIONAL ARRAY CHECKER
        ==============================

        +---+----------------------------------------------------------------------------------------------------------------------------+---------------------------------------------------------------------------------------------+
        | # | Message                                                                                                                    | Target                                                                                      |
        +---+----------------------------------------------------------------------------------------------------------------------------+---------------------------------------------------------------------------------------------+
        | 1 | Reached max level of nesting for the plugin registration in the {ApplicationDependencyProvider::getPlugins()}.             | SprykerSdkTest\ValidProject\MultidimensionalArray\Application\ApplicationDependencyProvider |
        |   | The maximum allowed nesting level is 2. Please, refactor code, otherwise it will cause upgradability issues in the future. |                                                                                             |
        +---+----------------------------------------------------------------------------------------------------------------------------+---------------------------------------------------------------------------------------------+

        Read more: https://docs.spryker.com/docs/scos/dev/guidelines/keeping-a-project-upgradable/upgradability-guidelines/multidimensional-array.html

        ===================
        PHP VERSION CHECKER
        ===================

        +---+-----------------------------------------------------------------------------+------------------------------------------------------------+
        | # | Message                                                                     | Target                                                     |
        +---+-----------------------------------------------------------------------------+------------------------------------------------------------+
        | 1 | Current PHP version "$phpVersion" is not allowed.                                 | Current php version $phpVersion                                  |
        +---+-----------------------------------------------------------------------------+------------------------------------------------------------+
        | 2 | Deploy file uses not allowed PHP image version "spryker/php:6.2-alpine3.12" | tests/Acceptance/_data/IgnoreErrorsCheckProject/deploy.yml |
        |   | Image tag must contain allowed PHP version (image:abc-8.0)                  |                                                            |
        +---+-----------------------------------------------------------------------------+------------------------------------------------------------+

        Read more: https://docs.spryker.com/docs/scos/dev/guidelines/keeping-a-project-upgradable/upgradability-guidelines/php-version.html


        OUT,
            $commandTester->getDisplay(),
        );
    }

    /**
     * @return void
     */
    public function testReturnSuccessWhenAllErrorsIgnored(): void
    {
        $commandTester = $this->createCommandTester('tests/Acceptance/_data/IgnoreErrorsCheckProject', ['EVALUATOR_TOOLING_FILE' => 'ignore_all_errors_tooling.yml']);
        $commandTester->execute(['--checkers' => implode(',', [MultidimensionalArrayChecker::NAME, PhpVersionChecker::NAME])]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }

    /**
     * @return void
     */
    public function testReturnViolationsWhenToolingFileDoesNotExist(): void
    {
        $commandTester = $this->createCommandTester('tests/Acceptance/_data/IgnoreErrorsCheckProject', ['EVALUATOR_TOOLING_FILE' => 'does_not_exists.yml']);
        $commandTester->execute(['--checkers' => implode(',', [DeadCodeChecker::NAME, PhpVersionChecker::NAME])]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
    }
}
