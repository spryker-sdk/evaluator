<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Console\Command;

use SprykerSdk\Evaluator\Builder\ToolingSettingsDtoBuilderInterface;
use SprykerSdk\Evaluator\Console\ReportRenderer\OutputReportRenderer;
use SprykerSdk\Evaluator\Console\ReportRenderer\ReportRenderResolver;
use SprykerSdk\Evaluator\Dto\EvaluatorInputDataDto;
use SprykerSdk\Evaluator\Dto\ToolingSettingsDto;
use SprykerSdk\Evaluator\Executor\EvaluatorExecutor;
use SprykerSdk\Evaluator\Filter\ReportFilterInterface;
use SprykerSdk\Evaluator\Reader\ToolingSettingsReaderInterface;
use SprykerSdk\Evaluator\Resolver\PathResolverInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EvaluatorCommand extends Command
{
    /**
     * @var string
     */
    public const COMMAND_NAME = 'evaluate';

    /**
     * @var string
     */
    protected const PATH_OPTION = 'path';

    /**
     * @var string
     */
    protected const FILE_OPTION = 'file';

    /**
     * @var string
     */
    protected const FORMAT_OPTION = 'format';

    /**
     * @var string
     */
    protected const CHECKERS_OPTION = 'checkers';

    /**
     * @var string
     */
    protected const EXCLUDE_CHECKERS_OPTION = 'exclude-checkers';

    /**
     * @var \SprykerSdk\Evaluator\Executor\EvaluatorExecutor
     */
    protected EvaluatorExecutor $evaluatorExecutor;

    /**
     * @var \SprykerSdk\Evaluator\Console\ReportRenderer\ReportRenderResolver
     */
    protected ReportRenderResolver $reportRenderResolver;

    /**
     * @var \SprykerSdk\Evaluator\Resolver\PathResolverInterface
     */
    protected PathResolverInterface $pathResolver;

    /**
     * @var \SprykerSdk\Evaluator\Builder\ToolingSettingsDtoBuilderInterface
     */
    protected ToolingSettingsDtoBuilderInterface $toolingSettingsDtoBuilder;

    /**
     * @var string $fileReport
     */
    protected string $fileReport;

    /**
     * @var \SprykerSdk\Evaluator\Reader\ToolingSettingsReaderInterface
     */
    protected ToolingSettingsReaderInterface $toolingSettingsReader;

    /**
     * @var \SprykerSdk\Evaluator\Filter\ReportFilterInterface
     */
    protected ReportFilterInterface $reportFilter;

    /**
     * @param \SprykerSdk\Evaluator\Executor\EvaluatorExecutor $evaluatorExecutor
     * @param \SprykerSdk\Evaluator\Console\ReportRenderer\ReportRenderResolver $reportRenderResolver
     * @param \SprykerSdk\Evaluator\Resolver\PathResolverInterface $pathResolver
     * @param \SprykerSdk\Evaluator\Builder\ToolingSettingsDtoBuilderInterface $toolingSettingsDtoBuilder
     * @param \SprykerSdk\Evaluator\Reader\ToolingSettingsReaderInterface $toolingSettingsReader
     * @param \SprykerSdk\Evaluator\Filter\ReportFilterInterface $reportFilter
     * @param string $fileReport
     */
    public function __construct(
        EvaluatorExecutor $evaluatorExecutor,
        ReportRenderResolver $reportRenderResolver,
        PathResolverInterface $pathResolver,
        ToolingSettingsDtoBuilderInterface $toolingSettingsDtoBuilder,
        ToolingSettingsReaderInterface $toolingSettingsReader,
        ReportFilterInterface $reportFilter,
        string $fileReport
    ) {
        parent::__construct(static::COMMAND_NAME);
        $this->evaluatorExecutor = $evaluatorExecutor;
        $this->reportRenderResolver = $reportRenderResolver;
        $this->pathResolver = $pathResolver;
        $this->toolingSettingsDtoBuilder = $toolingSettingsDtoBuilder;
        $this->toolingSettingsReader = $toolingSettingsReader;
        $this->fileReport = $fileReport;
        $this->reportFilter = $reportFilter;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->addOption(
                static::PATH_OPTION,
                null,
                InputOption::VALUE_OPTIONAL,
                'Run evaluator for specific folder or file',
                '',
            )
            ->addOption(
                static::FORMAT_OPTION,
                null,
                InputOption::VALUE_OPTIONAL,
                '',
                OutputReportRenderer::NAME,
            )
            ->addOption(
                static::FILE_OPTION,
                '-f',
                InputOption::VALUE_NEGATABLE,
                'Redirect output to a file',
                false,
            )
            ->addOption(
                static::CHECKERS_OPTION,
                null,
                InputOption::VALUE_OPTIONAL,
                'Run evaluator with specific checkers. Use comma to set multiply checkers',
            )
            ->addOption(
                static::EXCLUDE_CHECKERS_OPTION,
                null,
                InputOption::VALUE_OPTIONAL,
                'Run evaluator without specific checkers. Use comma to set multiply checkers',
            )
            ->setHelp('Run evaluator for the current project')
            ->setDescription('Run evaluator for the current project');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getOption(static::PATH_OPTION);
        $checkers = $this->getCheckersFromInput($input, static::CHECKERS_OPTION);
        $excludedCheckers = $this->getCheckersFromInput($input, static::EXCLUDE_CHECKERS_OPTION);
        $toolingSettings = $this->getToolingSettingsDto();

        $report = $this->evaluatorExecutor->execute(
            new EvaluatorInputDataDto(
                $this->pathResolver->resolvePath($path),
                $checkers,
                $excludedCheckers,
                $toolingSettings->getCheckerConfigs(),
            ),
        );

        $report = $this->reportFilter->filterReport($report, $toolingSettings);

        $this->reportRenderResolver
            ->resolve($input->getOption(static::FORMAT_OPTION))
            ->render(
                $report,
                $output,
                $input->getOption(static::FILE_OPTION) ? $this->pathResolver->createPath($this->fileReport) : null,
            );

        return $report->isSuccessful() ? static::SUCCESS : static::FAILURE;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param string $inputOption
     *
     * @return array<string>
     */
    protected function getCheckersFromInput(InputInterface $input, string $inputOption): array
    {
        return $input->getOption($inputOption)
            ? explode(',', (string)$input->getOption($inputOption))
            : [];
    }

    /**
     * @return \SprykerSdk\Evaluator\Dto\ToolingSettingsDto
     */
    protected function getToolingSettingsDto(): ToolingSettingsDto
    {
        return $this->toolingSettingsDtoBuilder->buildFromArray(
            $this->toolingSettingsReader->readFromFile(),
        );
    }
}
