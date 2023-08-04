<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Fetcher;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Checker\CheckerInterface;
use SprykerSdk\Evaluator\Checker\CheckerRegistryInterface;
use SprykerSdk\Evaluator\Dto\EvaluatorInputDataDto;
use SprykerSdk\Evaluator\Fetcher\CheckerFetcher;

class CheckerFetcherTest extends TestCase
{
    /**
     * @dataProvider checkersDataProvider
     *
     * @param array<string> $allowedCheckers
     * @param array<string> $excludedCheckers
     * @param array<string> $expectedResult
     *
     * @return void
     */
    public function testGetCheckersFilteredByInputDataShouldFetchProperCheckers(array $allowedCheckers, array $excludedCheckers, array $expectedResult): void
    {
        //Arrange
        $inputDataDto = new EvaluatorInputDataDto('', $allowedCheckers, $excludedCheckers);
        $checkerRegistryMock = $this->createCheckerRegistryMock(
            [
                $this->createCheckerMock('one'),
                $this->createCheckerMock('two'),
                $this->createCheckerMock('three'),
                $this->createCheckerMock('four'),
                $this->createCheckerMock('five'),
            ],
        );

        $checkersFetcher = new CheckerFetcher($checkerRegistryMock);

        //Act
        $fetchCheckers = $checkersFetcher->getCheckersFilteredByInputData($inputDataDto);

        //Assert
        $fetchCheckersNames = array_map(static fn (CheckerInterface $checker): string => $checker->getName(), $fetchCheckers);

        $this->assertSame($expectedResult, $fetchCheckersNames);
    }

    /**
     * @return array<mixed>
     */
    public function checkersDataProvider(): array
    {
        return [
            [['one', 'two'], [], ['one', 'two']],
            [['one', 'two'], ['one'], ['two']],
            [[], ['one', 'two'], ['three', 'four', 'five']],
        ];
    }

    /**
     * @param array<\SprykerSdk\Evaluator\Checker\CheckerInterface> $returnCheckers
     *
     * @return \SprykerSdk\Evaluator\Checker\CheckerRegistryInterface
     */
    protected function createCheckerRegistryMock(array $returnCheckers): CheckerRegistryInterface
    {
        $checkerRegistry = $this->createMock(CheckerRegistryInterface::class);
        $checkerRegistry->method('getAllCheckers')->willReturn($returnCheckers);

        return $checkerRegistry;
    }

    /**
     * @param string $name
     *
     * @return \SprykerSdk\Evaluator\Checker\CheckerInterface
     */
    protected function createCheckerMock(string $name): CheckerInterface
    {
        $checker = $this->createMock(CheckerInterface::class);
        $checker->method('getName')->willReturn($name);

        return $checker;
    }
}
