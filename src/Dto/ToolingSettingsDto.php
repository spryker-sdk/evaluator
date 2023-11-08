<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Dto;

class ToolingSettingsDto
{
    /**
     * @var array<\SprykerSdk\Evaluator\Dto\ToolingSettingsIgnoreErrorDto>
     */
    protected array $ignoreErrors;

    /**
     * @var array<\SprykerSdk\Evaluator\Dto\CheckerConfigDto>
     */
    protected array $checkerConfigs;

    /**
     * @param array<\SprykerSdk\Evaluator\Dto\ToolingSettingsIgnoreErrorDto> $ignoreErrors
     * @param array<\SprykerSdk\Evaluator\Dto\CheckerConfigDto> $checkerConfigs
     */
    public function __construct(array $ignoreErrors = [], array $checkerConfigs = [])
    {
        $this->ignoreErrors = $ignoreErrors;
        $this->checkerConfigs = $checkerConfigs;
    }

    /**
     * @return array<\SprykerSdk\Evaluator\Dto\ToolingSettingsIgnoreErrorDto>
     */
    public function getIgnoreErrors(): array
    {
        return $this->ignoreErrors;
    }

    /**
     * @return array<\SprykerSdk\Evaluator\Dto\CheckerConfigDto>
     */
    public function getCheckerConfigs(): array
    {
        return $this->checkerConfigs;
    }
}
