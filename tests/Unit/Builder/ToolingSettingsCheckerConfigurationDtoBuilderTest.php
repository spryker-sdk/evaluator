<?php

declare(strict_types=1);

namespace Unit\Builder;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Builder\ToolingSettingsCheckerConfigurationDtoBuilder;
use SprykerSdk\Evaluator\Dto\CheckerConfigDto;

class ToolingSettingsCheckerConfigurationDtoBuilderTest extends TestCase
{
    /**
     * @return void
     */
    public function testBuildFromToolingSettingsArrayShouldReturnEmptyArrayIfNoConfigKeySet(): void
    {
        // Arrange
        $yamlData = ['other_tool' => []];
        $builder = new ToolingSettingsCheckerConfigurationDtoBuilder('tooling.yml');

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
        $builder = new ToolingSettingsCheckerConfigurationDtoBuilder('tooling.yml');

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
            ToolingSettingsCheckerConfigurationDtoBuilder::CONFIGURATION_KEY => [
                [
                    ToolingSettingsCheckerConfigurationDtoBuilder::CHECKER_KEY => 'SOME_CHECKER',
                    ToolingSettingsCheckerConfigurationDtoBuilder::VAR_KEY => [
                        'VAR_ONE' => 'valueOne',
                    ],
                ],
            ],
        ];

        $builder = new ToolingSettingsCheckerConfigurationDtoBuilder('tooling.yml');

        // Act
        $result = $builder->buildFromToolingSettingsArray($yamlData);

        // Assert
        $this->assertCount(1, $result);

        $this->assertInstanceOf(CheckerConfigDto::class, $result[0]);
        $this->assertSame('SOME_CHECKER', $result[0]->getCheckerName());
        $this->assertSame(['VAR_ONE' => 'valueOne'], $result[0]->getConfig());
    }

    /**
     * @return array<mixed>
     */
    public static function invalidToolingSettingsDataDataProvider(): array
    {
        return [
            [[ToolingSettingsCheckerConfigurationDtoBuilder::CONFIGURATION_KEY => 111]],
            [[ToolingSettingsCheckerConfigurationDtoBuilder::CONFIGURATION_KEY => [111]]],
            [
                [
                    ToolingSettingsCheckerConfigurationDtoBuilder::CONFIGURATION_KEY => [
                        'some-key' => 'some-value',
                    ],
                ],
            ],
            [
                [
                    ToolingSettingsCheckerConfigurationDtoBuilder::CONFIGURATION_KEY => [
                        [
                            ToolingSettingsCheckerConfigurationDtoBuilder::CHECKER_KEY => [],
                        ],
                    ],
                ],
            ],
            [
                [
                    ToolingSettingsCheckerConfigurationDtoBuilder::CONFIGURATION_KEY => [
                        [
                        ToolingSettingsCheckerConfigurationDtoBuilder::CHECKER_KEY => null,
                            ],
                    ],
                ],
            ],
            [
                [
                    ToolingSettingsCheckerConfigurationDtoBuilder::CONFIGURATION_KEY => [
            [
                        ToolingSettingsCheckerConfigurationDtoBuilder::CHECKER_KEY => 'SOME_CHECKER',
                        ],
                    ],
                ],
            ],
        ];
    }
}
