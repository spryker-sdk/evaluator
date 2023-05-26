<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Report\Dto;

use SprykerSdk\Evaluator\Dto\ReportDto as EvaluatorReportDto;

class ReportPayloadDto
{
    /**
     * @var \SprykerSdk\Evaluator\Dto\ReportDto
     */
    protected EvaluatorReportDto $evaluatorReport;

    /**
     * ]
     *
     * @param \SprykerSdk\Evaluator\Dto\ReportDto $evaluatorReport
     */
    public function __construct(EvaluatorReportDto $evaluatorReport)
    {
        $this->evaluatorReport = $evaluatorReport;
    }

    /**
     * @return \SprykerSdk\Evaluator\Dto\ReportDto
     */
    public function getReport(): EvaluatorReportDto
    {
        return $this->evaluatorReport;
    }
}
