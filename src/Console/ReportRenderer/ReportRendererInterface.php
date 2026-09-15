<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Console\ReportRenderer;

use SprykerSdk\Evaluator\Dto\ReportDto;
use Symfony\Component\Console\Output\OutputInterface;

interface ReportRendererInterface
{
    public function getName(): string;

    public function render(ReportDto $report, OutputInterface $output, ?string $filePath = null): void;
}
