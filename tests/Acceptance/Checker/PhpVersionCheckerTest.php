<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Acceptance\Checker;

use SprykerSdk\Evaluator\Checker\PhpVersionChecker\PhpVersionChecker;
use SprykerSdkTest\Evaluator\Acceptance\ApplicationTestCase;
use SprykerSdkTest\Evaluator\Acceptance\TestHelper;
use Symfony\Component\Console\Command\Command;

/**
 * @group SprykerSdkTest
 * @group Evaluator
 * @group Acceptance
 * @group Checker
 * @group PhpVersionCheckerTest
 */
class PhpVersionCheckerTest extends ApplicationTestCase
{
    /**
     * @return void
     */
    public function testReturnSuccessOnValidProject(): void
    {
        $commandTester = $this->createCommandTester(TestHelper::VALID_PROJECT_PATH, ['PROJECT_PHP_VERSION' => '8.1']);
        $commandTester->execute(['--checkers' => PhpVersionChecker::NAME]);

        $commandTester->assertCommandIsSuccessful();
    }

    /**
     * @return void
     */
    public function testReturnViolationWhenProjectHasIssues(): void
    {
        $phpVersion = '6.6.6';
        $commandTester = $this->createCommandTester(TestHelper::INVALID_PROJECT_PATH, ['PROJECT_PHP_VERSION' => $phpVersion]);
        $commandTester->execute(['--checkers' => PhpVersionChecker::NAME]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertSame(
            <<<OUT
        ===================
        PHP VERSION CHECKER
        ===================

        +---+-----------------------------------------------------------------------------+--------------------------------------------------------+
        | # | Message                                                                     | Target                                                 |
        +---+-----------------------------------------------------------------------------+--------------------------------------------------------+
        | 1 | Current PHP version "$phpVersion" is not allowed.                                 | Current php version $phpVersion                              |
        +---+-----------------------------------------------------------------------------+--------------------------------------------------------+
        | 2 | Composer json PHP constraint ">=9.2" does not match allowed php versions    | tests/Acceptance/_data/InvalidProject/composer.json    |
        +---+-----------------------------------------------------------------------------+--------------------------------------------------------+
        | 3 | Deploy file uses not allowed PHP image version "spryker/php:6.4-alpine3.12" | tests/Acceptance/_data/InvalidProject/deploy.dev.yml   |
        |   | Image tag must contain allowed PHP version (image:abc-8.0)                  |                                                        |
        +---+-----------------------------------------------------------------------------+--------------------------------------------------------+
        | 4 | Deploy file uses not allowed PHP image version "spryker/php:6.2-alpine3.12" | tests/Acceptance/_data/InvalidProject/deploy.yml       |
        |   | Image tag must contain allowed PHP version (image:abc-8.0)                  |                                                        |
        +---+-----------------------------------------------------------------------------+--------------------------------------------------------+
        | 5 | Not all the targets have same PHP versions                                  | Current php version $phpVersion: -                           |
        |   |                                                                             | tests/Acceptance/_data/InvalidProject/composer.json: - |
        |   |                                                                             | tests/Acceptance/_data/InvalidProject/deploy**.yml: -  |
        |   |                                                                             | SDK php versions: php8                                 |
        +---+-----------------------------------------------------------------------------+--------------------------------------------------------+

        Read more: https://docs.spryker.com/docs/scos/dev/guidelines/keeping-a-project-upgradable/upgradability-guidelines/php-version.html


        OUT,
            $commandTester->getDisplay(),
        );
    }
}
