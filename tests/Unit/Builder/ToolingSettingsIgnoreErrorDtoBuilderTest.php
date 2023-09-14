<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Builder;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Builder\ToolingSettingsIgnoreErrorDtoBuilder;
use SprykerSdk\Evaluator\Dto\ToolingSettingsIgnoreErrorDto;

class ToolingSettingsIgnoreErrorDtoBuilderTest extends TestCase
{
    /**
     * @return void
     */
    public function testBuildFromToolingSettingsArrayShouldReturnEmptyArrayIfNoConfigKeySet(): void
    {
        // Arrange
        $yamlData = ['other_tool' => []];
        $builder = new ToolingSettingsIgnoreErrorDtoBuilder('tooling.yml');

        // Act
        $result = $builder->buildFromToolingSettingsArray($yamlData);

        // Assert
        $this->assertEmpty($result);
    }

    /**
     * @dataProvider invalidToolingSettingsDataDataProvider
     *
     * @param array<mixed> $yamlData
     *
     * @return void
     */
    public function testBuildFromToolingSettingsArrayShouldThrowExceptionsOnInvalidFormat(array $yamlData): void
    {
        // Arrange
        $this->expectException(InvalidArgumentException::class);
        $builder = new ToolingSettingsIgnoreErrorDtoBuilder('tooling.yml');

        // Act
        $builder->buildFromToolingSettingsArray($yamlData);
    }

    /**
     * @return void
     */
    public function testBuildFromToolingSettingsArrayShouldValidArray(): void
    {
        // Arrange
        $yamlData = [
                ToolingSettingsIgnoreErrorDtoBuilder::IGNORE_ERRORS_KEY => [
                    'regexpOne',
                    [
                        ToolingSettingsIgnoreErrorDtoBuilder::MESSAGES_KEY => ['regexpTwo'],
                        ToolingSettingsIgnoreErrorDtoBuilder::CHECKER_KEY => 'SOME_CHECKER',
                    ],
                ],
            ];

        $builder = new ToolingSettingsIgnoreErrorDtoBuilder('tooling.yml');

        // Act
        $result = $builder->buildFromToolingSettingsArray($yamlData);

        // Assert
        $this->assertCount(2, $result);

        $this->assertInstanceOf(ToolingSettingsIgnoreErrorDto::class, $result[0]);
        $this->assertSame(['regexpOne'], $result[0]->getMessageRegexps());
        $this->assertNull($result[0]->getCheckerName());

        $this->assertInstanceOf(ToolingSettingsIgnoreErrorDto::class, $result[1]);
        $this->assertSame(['regexpTwo'], $result[1]->getMessageRegexps());
        $this->assertSame('SOME_CHECKER', $result[1]->getCheckerName());
    }

    /**
     * @return array<mixed>
     */
    protected function invalidToolingSettingsDataDataProvider(): array
    {
        return [
            [[ToolingSettingsIgnoreErrorDtoBuilder::IGNORE_ERRORS_KEY => 111]],
            [[ToolingSettingsIgnoreErrorDtoBuilder::IGNORE_ERRORS_KEY => [111]]],
            [
                [
                    ToolingSettingsIgnoreErrorDtoBuilder::IGNORE_ERRORS_KEY => [
                        'regexp',
                        [ToolingSettingsIgnoreErrorDtoBuilder::MESSAGES_KEY => []],
                    ],
                ],
            ],
            [
                [
                    ToolingSettingsIgnoreErrorDtoBuilder::IGNORE_ERRORS_KEY => [
                        'regexp',
                        [ToolingSettingsIgnoreErrorDtoBuilder::CHECKER_KEY => 'checker'],
                    ],
                ],
            ],
            [
                [
                    ToolingSettingsIgnoreErrorDtoBuilder::IGNORE_ERRORS_KEY => [
                        'regexp',
                        [
                            ToolingSettingsIgnoreErrorDtoBuilder::MESSAGES_KEY => [111, 'regexp'],
                            ToolingSettingsIgnoreErrorDtoBuilder::CHECKER_KEY => 'checker',
                        ],
                    ],
                ],
            ],
            [
                [
                    ToolingSettingsIgnoreErrorDtoBuilder::IGNORE_ERRORS_KEY => [
                        'regexp',
                        [
                            ToolingSettingsIgnoreErrorDtoBuilder::MESSAGES_KEY => ['regexp'],
                            ToolingSettingsIgnoreErrorDtoBuilder::CHECKER_KEY => 111,
                        ],
                    ],
                ],
            ],
        ];
    }
}
