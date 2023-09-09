<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Acceptance\Checker;

use SprykerSdk\Evaluator\Checker\ContainerSetFunctionChecker\ContainerSetFunctionChecker;
use SprykerSdkTest\Evaluator\Acceptance\ApplicationTestCase;
use SprykerSdkTest\Evaluator\Acceptance\TestHelper;
use Symfony\Component\Console\Command\Command;

/**
 * @group SprykerSdkTest
 * @group Evaluator
 * @group Acceptance
 * @group Checker
 * @group ContainerSetFunctionCheckerTest
 */
class ContainerSetFunctionCheckerTest extends ApplicationTestCase
{
    /**
     * @return void
     */
    public function testReturnSuccessOnValidProject(): void
    {
        $commandTester = $this->createCommandTester(TestHelper::VALID_PROJECT_PATH);
        $commandTester->execute(['--checkers' => ContainerSetFunctionChecker::NAME]);

        $commandTester->assertCommandIsSuccessful();
    }

    /**
     * @return void
     */
    public function testReturnViolationWhenProjectHasIssues(): void
    {
        $commandTester = $this->createCommandTester(TestHelper::INVALID_PROJECT_PATH);
        $commandTester->execute(['--checkers' => ContainerSetFunctionChecker::NAME]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());

        $this->assertSame(
            <<<OUT
        ==============================
        CONTAINER SET FUNCTION CHECKER
        ==============================

        +---+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+----------------------------------------------------------------------------------------------------------------+
        | # | Message                                                                                                                                                                      | Target                                                                                                         |
        +---+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+----------------------------------------------------------------------------------------------------------------+
        | 1 | The callback function inside `container->set()` should not return an array directly but instead call another method. Please review your code and make the necessary changes. | tests/Acceptance/_data/InvalidProject/src/Pyz/Zed/ContainerSetFunctionChecker/ExampleDependencyProvider.php:17 |
        +---+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+----------------------------------------------------------------------------------------------------------------+

        Read more: https://docs.spryker.com/docs/scos/dev/guidelines/keeping-a-project-upgradable/upgradability-guidelines/additional-logic-in-dependency-provider.html


        OUT,
            $commandTester->getDisplay(),
        );
    }
}
