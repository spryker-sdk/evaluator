<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Console\ReportRenderer;

use SprykerSdk\Evaluator\Dto\ReportDto;
use SprykerSdk\Evaluator\Dto\ReportLineDto;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class CompactReportRenderer extends AbstractOutputReport
{
    /**
     * @var string
     */
    public const NAME = 'compact';

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

        foreach ($report->getReportLines() as $reportLine) {
            $this->renderTitle($reportLine, $output);

            $output->writeln('');

            $reportLine->getViolations()
                ? $this->renderCheckerViolations($reportLine->getViolations(), $output)
                : $this->renderSuccess($output);

            if ($reportLine->getViolations() && $reportLine->getDocUrl() !== '') {
                $this->renderDocUrl($reportLine->getDocUrl(), $output);
            }

            $output->writeln('');
        }

        if ($filePath && $output instanceof BufferedOutput) {
            $this->saveToFile($filePath, $output->fetch());
        }
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\ReportLineDto $reportLine
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    protected function renderTitle(ReportLineDto $reportLine, OutputInterface $output): void
    {
        $checkerName = (string)preg_replace('/[\W_]/', ' ', $reportLine->getCheckerName());
        $separator = str_repeat('=', strlen($checkerName));

        $output->writeln($separator);
        $output->writeln(strtoupper($checkerName));
        $output->writeln($separator);
    }

    /**
     * @param array<\SprykerSdk\Evaluator\Dto\ViolationDto> $violations
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    protected function renderCheckerViolations(array $violations, OutputInterface $output): void
    {
        foreach ($violations as $violation) {
            $table = new Table($output);
            $table->addRow(['Message:', $violation->getMessage()]);
            $table->addRow([' Target:', $violation->getTarget()]);
            $table->addRow(['']);
            $table->setStyle('compact');
            $table->render();
        }
    }

    /**
     * @param string $docUrl
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    protected function renderDocUrl(string $docUrl, OutputInterface $output): void
    {
        $output->writeln('');
        $output->writeln(sprintf('Read more: %s', $docUrl));
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    protected function renderSuccess(OutputInterface $output): void
    {
        $output->writeln('<bg=green>Success!</>');
    }
}
