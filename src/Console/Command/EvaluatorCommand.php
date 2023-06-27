<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Console\Command;

use SprykerSdk\Evaluator\Console\ReportRenderer\CompactReportRenderer;
use SprykerSdk\Evaluator\Console\ReportRenderer\ReportRenderResolver;
use SprykerSdk\Evaluator\Dto\EvaluatorInputDataDto;
use SprykerSdk\Evaluator\Executor\EvaluatorExecutor;
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
     * @var string $fileReport
     */
    protected string $fileReport;

    /**
     * @param \SprykerSdk\Evaluator\Executor\EvaluatorExecutor $evaluatorExecutor
     * @param \SprykerSdk\Evaluator\Console\ReportRenderer\ReportRenderResolver $reportRenderResolver
     * @param \SprykerSdk\Evaluator\Resolver\PathResolverInterface $pathResolver
     * @param string $fileReport
     */
    public function __construct(
        EvaluatorExecutor $evaluatorExecutor,
        ReportRenderResolver $reportRenderResolver,
        PathResolverInterface $pathResolver,
        string $fileReport
    ) {
        parent::__construct(static::COMMAND_NAME);
        $this->evaluatorExecutor = $evaluatorExecutor;
        $this->reportRenderResolver = $reportRenderResolver;
        $this->pathResolver = $pathResolver;
        $this->fileReport = $fileReport;
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
                CompactReportRenderer::NAME,
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

        $checkers = $input->getOption(static::CHECKERS_OPTION)
            ? explode(',', (string)$input->getOption(static::CHECKERS_OPTION))
            : [];

        $report = $this->evaluatorExecutor->execute(
            new EvaluatorInputDataDto($this->pathResolver->resolvePath($path), $checkers),
        );

        $this->reportRenderResolver
            ->resolve($input->getOption(static::FORMAT_OPTION))
            ->render(
                $report,
                $output,
                $input->getOption(static::FILE_OPTION) ? $this->pathResolver->createPath($this->fileReport) : null,
            );

        return $report->isSuccessful() ? static::SUCCESS : static::FAILURE;
    }
}
