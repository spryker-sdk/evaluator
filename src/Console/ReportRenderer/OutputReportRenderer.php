<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Console\ReportRenderer;

use SprykerSdk\Evaluator\Dto\DebugInfoDto;
use SprykerSdk\Evaluator\Dto\ReportDto;
use SprykerSdk\Evaluator\Dto\ReportLineDto;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class OutputReportRenderer extends AbstractOutputReport
{
    /**
     * @var int
     */
    protected const BYTES_IN_MB = 1024 * 1024;

    /**
     * @var string
     */
    public const NAME = 'output';

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

            if ($output->isVerbose() && $reportLine->getDebugInfo()) {
                $this->renderDebugInfo($reportLine->getDebugInfo(), $output);
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
        $table = new Table($output);
        $table
            ->setHeaders(['#', 'Message', 'Target'])
            ->setRows($this->getRows($violations));
        $table->render();
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
     * @param \SprykerSdk\Evaluator\Dto\DebugInfoDto $debugInfo
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    protected function renderDebugInfo(DebugInfoDto $debugInfo, OutputInterface $output): void
    {
        $output->writeln('');
        $output->writeln(sprintf('%.2F MiB - %d ms', $debugInfo->getMemoryInBytes() / static::BYTES_IN_MB, $debugInfo->getDurationInMs()));
    }

    /**
     * @param array<\SprykerSdk\Evaluator\Dto\ViolationDto> $violations
     *
     * @return array<mixed>
     */
    protected function getRows(array $violations): array
    {
        $rows = [];

        foreach ($violations as $index => $violation) {
            $rows[] = [++$index, $violation->getMessage(), $violation->getTarget()];
            $rows[] = new TableSeparator();
        }

        array_pop($rows);

        return $rows;
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
