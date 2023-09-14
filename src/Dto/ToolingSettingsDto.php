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
     * @var \SprykerSdk\Evaluator\Dto\ToolingSettingsIgnoreErrorDto[]
     */
    protected array $ignoreErrors;

    /**
     * @param array<\SprykerSdk\Evaluator\Dto\ToolingSettingsIgnoreErrorDto> $ignoreErrors
     */
    public function __construct(array $ignoreErrors = [])
    {
        $this->ignoreErrors = $ignoreErrors;
    }

    /**
     * @return array<\SprykerSdk\Evaluator\Dto\ToolingSettingsIgnoreErrorDto>
     */
    public function getIgnoreErrors(): array
    {
        return $this->ignoreErrors;
    }
}
