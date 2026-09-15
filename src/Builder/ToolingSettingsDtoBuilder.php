<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Builder;

use SprykerSdk\Evaluator\Dto\ToolingSettingsDto;

class ToolingSettingsDtoBuilder implements ToolingSettingsDtoBuilderInterface
{
    /**
     * @var string
     */
    protected const EVALUATOR_KEY = 'evaluator';

    protected ToolingSettingsIgnoreErrorDtoBuilder $toolingSettingsIgnoreErrorDtoBuilder;

    protected ToolingSettingsCheckerConfigurationDtoBuilder $toolingSettingsCheckerConfigurationDtoBuilder;

    public function __construct(
        ToolingSettingsIgnoreErrorDtoBuilder $toolingSettingsIgnoreErrorDtoBuilder,
        ToolingSettingsCheckerConfigurationDtoBuilder $toolingSettingsCheckerConfigurationDtoBuilder
    ) {
        $this->toolingSettingsIgnoreErrorDtoBuilder = $toolingSettingsIgnoreErrorDtoBuilder;
        $this->toolingSettingsCheckerConfigurationDtoBuilder = $toolingSettingsCheckerConfigurationDtoBuilder;
    }

    /**
     * @param array<mixed> $toolingSettings
     */
    public function buildFromArray(array $toolingSettings): ToolingSettingsDto
    {
        return new ToolingSettingsDto(
            $this->toolingSettingsIgnoreErrorDtoBuilder->buildFromToolingSettingsArray($toolingSettings[static::EVALUATOR_KEY] ?? []),
            $this->toolingSettingsCheckerConfigurationDtoBuilder->buildFromToolingSettingsArray($toolingSettings[static::EVALUATOR_KEY] ?? []),
        );
    }
}
