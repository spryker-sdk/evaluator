<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Acceptance\Checker;

use SprykerSdk\Evaluator\Checker\MultidimensionalArrayChecker\MultidimensionalArrayChecker;
use SprykerSdkTest\Evaluator\Acceptance\ApplicationTestCase;
use SprykerSdkTest\Evaluator\Acceptance\TestHelper;
use Symfony\Component\Console\Command\Command;

/**
 * @group SprykerSdkTest
 * @group Evaluator
 * @group Acceptance
 * @group Checker
 * @group MultidimensionalArrayCheckerTest
 */
class MultidimensionalArrayCheckerTest extends ApplicationTestCase
{
    /**
     * @return void
     */
    public function testReturnSuccessOnValidProject(): void
    {
        $commandTester = $this->createCommandTester(TestHelper::VALID_PROJECT_PATH);
        $commandTester->execute(['--checkers' => MultidimensionalArrayChecker::NAME, '--format' => 'json']);

        $commandTester->assertCommandIsSuccessful();
        $this->assertSame('[]', $commandTester->getDisplay());
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
        $commandTester = $this->createCommandTester(TestHelper::INVALID_PROJECT_PATH);
        $commandTester->execute(['--checkers' => MultidimensionalArrayChecker::NAME, '--format' => 'json', '--path' => $path]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());

        $this->assertStringContainsString(
            'https:\/\/docs.spryker.com\/docs\/scos\/dev\/guidelines\/keeping-a-project-upgradable\/upgradability-guidelines\/multidimensional-array.html',
            $commandTester->getDisplay(),
            'The output must contain correct link.',
        );
    }

    /**
     * @return array<string, array<string>>
     */
    public static function differentIssueCases(): array
    {
        return [
            'return multidimensional array' => ['src/Pyz/Zed/MultidimensionalArray/ReturnArray'],
            'assign multidimensional array' => ['src/Pyz/Zed/MultidimensionalArray/AssignArray'],
            'merge multidimensional array' => ['src/Pyz/Zed/MultidimensionalArray/ArrayMerge'],
        ];
    }
}
