<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Console\ReportRenderer;

use SprykerSdk\Evaluator\Dto\ReportDto;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class JsonReportRenderer extends AbstractOutputReport
{
    /**
     * @var string
     */
    protected const EXTENSION = 'json';

    /**
     * @var string
     */
    public const NAME = 'json';

    /**
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\ReportDto $report
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param string|null $filePath
     *
     * @return void
     */
    public function render(ReportDto $report, OutputInterface $output, ?string $filePath = null): void
    {
        if ($filePath) {
            $output = new BufferedOutput();
        }
        $reportData = [];
        foreach ($report->getReportLines() as $reportLine) {
            if (!$reportLine->getViolations()) {
                continue;
            }
            $checkerReport = [];
            $checkerReport['docUrl'] = $reportLine->getDocUrl();
            foreach ($reportLine->getViolations() as $violation) {
                $checkerReport['violation']['target'] = $violation->getTarget();
                $checkerReport['violation']['message'] = $violation->getMessage();
            }
            $reportData[$reportLine->getCheckerName()] = $checkerReport;
        }
        $output->write((string)json_encode($reportData));

        if ($filePath && $output instanceof BufferedOutput) {
            $this->saveToFile($filePath, $output->fetch());
        }
    }
}
