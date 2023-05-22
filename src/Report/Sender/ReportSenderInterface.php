<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Report\Sender;

use SprykerSdk\Evaluator\Report\Dto\ReportDto;

interface ReportSenderInterface
{
    /**
     * @param \SprykerSdk\Evaluator\Report\Dto\ReportDto $reportDto
     *
     * @return void
     */
    public function send(ReportDto $reportDto): void;
}
