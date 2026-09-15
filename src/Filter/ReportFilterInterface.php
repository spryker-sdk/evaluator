<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Filter;

use SprykerSdk\Evaluator\Dto\ReportDto;
use SprykerSdk\Evaluator\Dto\ToolingSettingsDto;

interface ReportFilterInterface
{
    public function filterReport(ReportDto $reportDto, ToolingSettingsDto $toolingSettingsDto): ReportDto;
}
