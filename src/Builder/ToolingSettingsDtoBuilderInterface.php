<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Builder;

use SprykerSdk\Evaluator\Dto\ToolingSettingsDto;

interface ToolingSettingsDtoBuilderInterface
{
    /**
     * @param array<mixed> $toolingSettings
     *
     * @return \SprykerSdk\Evaluator\Dto\ToolingSettingsDto
     */
    public function buildFromArray(array $toolingSettings): ToolingSettingsDto;
}
