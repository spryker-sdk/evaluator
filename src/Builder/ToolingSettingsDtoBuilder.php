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

    /**
     * @var \SprykerSdk\Evaluator\Builder\ToolingSettingsIgnoreErrorDtoBuilder
     */
    protected ToolingSettingsIgnoreErrorDtoBuilder $toolingSettingsIgnoreErrorDtoBuilder;

    /**
     * @param \SprykerSdk\Evaluator\Builder\ToolingSettingsIgnoreErrorDtoBuilder $toolingSettingsIgnoreErrorDtoBuilder
     */
    public function __construct(ToolingSettingsIgnoreErrorDtoBuilder $toolingSettingsIgnoreErrorDtoBuilder)
    {
        $this->toolingSettingsIgnoreErrorDtoBuilder = $toolingSettingsIgnoreErrorDtoBuilder;
    }

    /**
     * @param array<mixed> $toolingSettings
     *
     * @return \SprykerSdk\Evaluator\Dto\ToolingSettingsDto
     */
    public function buildFromArray(array $toolingSettings): ToolingSettingsDto
    {
        return new ToolingSettingsDto(
            $this->toolingSettingsIgnoreErrorDtoBuilder->buildFromToolingSettingsArray($toolingSettings[static::EVALUATOR_KEY] ?? []),
        );
    }
}
