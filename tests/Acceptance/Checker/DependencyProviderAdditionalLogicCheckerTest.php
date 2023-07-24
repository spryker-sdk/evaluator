<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Acceptance\Checker;

use SprykerSdk\Evaluator\Checker\DependencyProviderAdditionalLogicChecker\DependencyProviderAdditionalLogicChecker;
use SprykerSdkTest\Evaluator\Acceptance\ApplicationTestCase;
use SprykerSdkTest\Evaluator\Acceptance\TestHelper;
use Symfony\Component\Console\Command\Command;

/**
 * @group SprykerSdkTest
 * @group Evaluator
 * @group Acceptance
 * @group Checker
 * @group DependencyProviderAdditionalLogicCheckerTest
 */
class DependencyProviderAdditionalLogicCheckerTest extends ApplicationTestCase
{
    /**
     * @return void
     */
    public function testReturnSuccessOnValidProject(): void
    {
        $commandTester = $this->createCommandTester(TestHelper::VALID_PROJECT_PATH);
        $commandTester->execute(['--checkers' => DependencyProviderAdditionalLogicChecker::NAME]);

        $commandTester->assertCommandIsSuccessful();
    }

    /**
     * @return void
     */
    public function testReturnViolationWhenProjectHasIssues(): void
    {
        $commandTester = $this->createCommandTester(TestHelper::INVALID_PROJECT_PATH);
        $commandTester->execute(['--checkers' => DependencyProviderAdditionalLogicChecker::NAME]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertSame(
            <<<OUT
        ============================================
        DEPENDENCY PROVIDER ADDITIONAL LOGIC CHECKER
        ============================================

        +---+------------------------------------------------------------------------------------+--------------------------------------------------------------------------------------------------------------------------+
        | # | Message                                                                            | Target                                                                                                                   |
        +---+------------------------------------------------------------------------------------+--------------------------------------------------------------------------------------------------------------------------+
        | 1 | The if (!static::IS_DEV) {} condition statement is forbidden in DependencyProvider | tests/Acceptance/_data/InvalidProject/src/Pyz/Zed/DependencyProviderAdditionalLogicChecker/ConsoleDependencyProvider.php |
        +---+------------------------------------------------------------------------------------+--------------------------------------------------------------------------------------------------------------------------+

        Read more: https://docs.spryker.com/docs/scos/dev/guidelines/keeping-a-project-upgradable/upgradability-guidelines/additional-logic-in-dependency-provider.html


        OUT,
            $commandTester->getDisplay(),
        );
    }
}
