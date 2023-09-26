<?php


declare(strict_types=1);

namespace Acceptance\Checker;

use SprykerSdk\Evaluator\Checker\DevPackagesChecker\SprykerDevPackagesChecker;
use SprykerSdkTest\Evaluator\Acceptance\ApplicationTestCase;
use SprykerSdkTest\Evaluator\Acceptance\TestHelper;
use Symfony\Component\Console\Command\Command;

/**
 * @group Acceptance
 * @group Checker
 * @group SprykerDevPackagesCheckerTest
 */
class SprykerDevPackagesCheckerTest extends ApplicationTestCase
{
    /**
     * @return void
     */
    public function testReturnSuccessOnValidProject(): void
    {
        $commandTester = $this->createCommandTester(TestHelper::VALID_PROJECT_PATH);
        $commandTester->execute(['--checkers' => SprykerDevPackagesChecker::NAME]);

        $commandTester->assertCommandIsSuccessful();
    }

    /**
     * @return void
     */
    public function testReturnViolationWhenProjectHasIssues(): void
    {
        $commandTester = $this->createCommandTester(TestHelper::INVALID_PROJECT_PATH);
        $commandTester->execute(['--checkers' => SprykerDevPackagesChecker::NAME]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertSame(
            <<<OUT
        ============================
        SPRYKER DEV PACKAGES CHECKER
        ============================

        +---+-----------------------------------------------------------------------------------------+-------------------+
        | # | Message                                                                                 | Target            |
        +---+-----------------------------------------------------------------------------------------+-------------------+
        | 1 | Spryker package "spryker/uuid:dev-some-branch" has forbidden "dev-*" version constraint | spryker/uuid      |
        +---+-----------------------------------------------------------------------------------------+-------------------+
        | 2 | Spryker package "spryker-shop/cart:dev-master" has forbidden "dev-*" version constraint | spryker-shop/cart |
        +---+-----------------------------------------------------------------------------------------+-------------------+

        Read more: https://docs.spryker.com/docs/scos/dev/guidelines/keeping-a-project-upgradable/upgradability-guidelines/spryker-dev-packages-checker.html


        OUT,
            $commandTester->getDisplay(),
        );
    }
}
