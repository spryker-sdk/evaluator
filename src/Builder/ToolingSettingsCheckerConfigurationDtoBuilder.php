<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Builder;

use InvalidArgumentException;
use SprykerSdk\Evaluator\Dto\CheckerConfigDto;

class ToolingSettingsCheckerConfigurationDtoBuilder
{
    /**
     * @var string
     */
    public const CONFIGURATION_KEY = 'configuration';

    /**
     * @var string
     */
    public const CHECKER_KEY = 'checker';

    /**
     * @var string
     */
    public const VAR_KEY = 'var';

    /**
     * @var string
     */
    protected string $toolingFile;

    /**
     * @param string $toolingFile
     */
    public function __construct(string $toolingFile)
    {
        $this->toolingFile = $toolingFile;
    }

    /**
     * @param array<mixed>$toolingSettingsArray
     *
     * @throws \InvalidArgumentException
     *
     * @return array<\SprykerSdk\Evaluator\Dto\CheckerConfigDto>
     */
    public function buildFromToolingSettingsArray(array $toolingSettingsArray): array
    {
        if (!isset($toolingSettingsArray[static::CONFIGURATION_KEY])) {
            return [];
        }

        if (!is_array($toolingSettingsArray[static::CONFIGURATION_KEY])) {
            throw new InvalidArgumentException(sprintf('Key [%s] should be an array in %s', static::CONFIGURATION_KEY, $this->toolingFile));
        }

        $ignoreErrorDtos = [];

        foreach ($toolingSettingsArray[static::CONFIGURATION_KEY] as $checkerConfiguration) {
            if (!is_array($checkerConfiguration)) {
                throw new InvalidArgumentException(sprintf('Element of key [%s] should be an array in %s', static::CONFIGURATION_KEY, $this->toolingFile));
            }

            $ignoreErrorDtos[] = $this->buildConfigurationDto($checkerConfiguration);
        }

        return $ignoreErrorDtos;
    }

    /**
     * @param array<mixed> $checkerConfiguration
     *
     * @throws \InvalidArgumentException
     *
     * @return \SprykerSdk\Evaluator\Dto\CheckerConfigDto
     */
    protected function buildConfigurationDto(array $checkerConfiguration): CheckerConfigDto
    {
        if (!isset($checkerConfiguration[static::CHECKER_KEY])) {
            throw new InvalidArgumentException(sprintf('Required key [%s][%s] is not found in %s', static::CONFIGURATION_KEY, static::CHECKER_KEY, $this->toolingFile));
        }

        if (!isset($checkerConfiguration[static::VAR_KEY])) {
            throw new InvalidArgumentException(sprintf('Required key [%s][%s] is not found in %s', static::CONFIGURATION_KEY, static::VAR_KEY, $this->toolingFile));
        }

        return new CheckerConfigDto(
            $checkerConfiguration[static::CHECKER_KEY],
            $checkerConfiguration[static::VAR_KEY] ?? [],
        );
    }
}
