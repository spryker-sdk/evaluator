<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Report\Builder;

use SprykerSdk\Evaluator\Dto\ReportDto as EvaluatorReportDto;
use SprykerSdk\Evaluator\Report\Dto\ReportDto;

interface ReportDtoBuilderInterface
{
    /**
     * @param \SprykerSdk\Evaluator\Dto\ReportDto $evaluatorReportDto
     *
     * @return \SprykerSdk\Evaluator\Report\Dto\ReportDto
     */
    public function buildReportDto(EvaluatorReportDto $evaluatorReportDto): ReportDto;
}
