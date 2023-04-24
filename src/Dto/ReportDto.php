<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Dto;

class ReportDto
{
    /**
     * @var array<\SprykerSdk\Evaluator\Dto\ReportLineDto>
     */
    protected array $reportLines;

    /**
     * @param array<\SprykerSdk\Evaluator\Dto\ReportLineDto> $reportLines
     */
    public function __construct(array $reportLines = [])
    {
        $this->reportLines = $reportLines;
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\ReportLineDto $reportLine
     *
     * @return void
     */
    public function addReportLine(ReportLineDto $reportLine): void
    {
        $this->reportLines[] = $reportLine;
    }

    /**
     * @return array<\SprykerSdk\Evaluator\Dto\ReportLineDto>
     */
    public function getReportLines(): array
    {
        return $this->reportLines;
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return !array_filter($this->reportLines, static fn (ReportLineDto $reportLine): bool => !$reportLine->isSuccessful());
    }
}
