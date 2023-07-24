<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Acceptance\Checker;

use SprykerSdk\Evaluator\Checker\PluginsRegistrationWithRestrictionsChecker\PluginsRegistrationWithRestrictionsChecker;
use SprykerSdkTest\Evaluator\Acceptance\ApplicationTestCase;
use SprykerSdkTest\Evaluator\Acceptance\TestHelper;
use Symfony\Component\Console\Command\Command;

/**
 * @group SprykerSdkTest
 * @group Evaluator
 * @group Acceptance
 * @group Checker
 * @group PluginsRegistrationWithRestrictionsCheckerTest
 */
class PluginsRegistrationWithRestrictionsCheckerTest extends ApplicationTestCase
{
    /**
     * @return void
     */
    public function testReturnSuccessOnValidProject(): void
    {
        $commandTester = $this->createCommandTester(TestHelper::VALID_PROJECT_PATH);
        $commandTester->execute(['--checkers' => PluginsRegistrationWithRestrictionsChecker::NAME, '--path' => 'src/Pyz/Zed/PluginsRegistrationWithRestrictionsChecker']);

        $commandTester->assertCommandIsSuccessful();
    }

    /**
     * @return void
     */
    public function testReturnViolationWhenProjectHasIssues(): void
    {
        $commandTester = $this->createCommandTester(TestHelper::INVALID_PROJECT_PATH);
        $commandTester->execute(['--checkers' => PluginsRegistrationWithRestrictionsChecker::NAME, '--path' => 'src/Pyz/Zed/PluginsRegistrationWithRestrictionsChecker']);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());

        $this->assertSame(
            <<<OUT
        ==============================================
        PLUGINS REGISTRATION WITH RESTRICTIONS CHECKER
        ==============================================

        +---+--------------------------------------------------------------------------------------------------------------------------------+-----------------------------------+
        | # | Message                                                                                                                        | Target                            |
        +---+--------------------------------------------------------------------------------------------------------------------------------+-----------------------------------+
        | 1 | Class "\Spryker\Zed\CategoryNavigationConnector\Communication\Plugin\InvalidPlagin" is not used in current dependency provider | CategoryDependencyProvider.php:25 |
        +---+--------------------------------------------------------------------------------------------------------------------------------+-----------------------------------+
        | 2 | Restriction rule does not match the pattern "/^\* - (before|after) \{@link (?<class>.+)\}( .*\.|)$/"                           | CategoryDependencyProvider.php:25 |
        +---+--------------------------------------------------------------------------------------------------------------------------------+-----------------------------------+
        | 3 | Restriction rule does not match the pattern "/^\* - (before|after) \{@link (?<class>.+)\}( .*\.|)$/"                           | CategoryDependencyProvider.php:47 |
        +---+--------------------------------------------------------------------------------------------------------------------------------+-----------------------------------+

        Read more: https://docs.spryker.com/docs/scos/dev/guidelines/keeping-a-project-upgradable/upgradability-guidelines/plugin-registration-with-restrintions.html


        OUT,
            $commandTester->getDisplay(),
        );
    }
}
