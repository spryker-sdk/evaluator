<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Report;

use SprykerSdk\Evaluator\Dto\ReportDto;

interface ReportSendProcessorInterface
{
    /**
     * @param \SprykerSdk\Evaluator\Dto\ReportDto $reportDto
     *
     * @return void
     */
    public function process(ReportDto $reportDto): void;
}
