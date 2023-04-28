<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Console\Command;

use SprykerSdk\Evaluator\Console\ReportRenderer\ReportRenderer;
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
    protected const CHECKERS_OPTION = 'checkers';

    /**
     * @var \SprykerSdk\Evaluator\Executor\EvaluatorExecutor
     */
    protected EvaluatorExecutor $evaluatorExecutor;

    /**
     * @var \SprykerSdk\Evaluator\Console\ReportRenderer\ReportRenderer
     */
    protected ReportRenderer $reportRenderer;

    /**
     * @var \SprykerSdk\Evaluator\Resolver\PathResolverInterface
     */
    protected PathResolverInterface $pathResolver;

    /**
     * @param \SprykerSdk\Evaluator\Executor\EvaluatorExecutor $evaluatorExecutor
     * @param \SprykerSdk\Evaluator\Console\ReportRenderer\ReportRenderer $reportRenderer
     * @param \SprykerSdk\Evaluator\Resolver\PathResolverInterface $pathResolver
     */
    public function __construct(EvaluatorExecutor $evaluatorExecutor, ReportRenderer $reportRenderer, PathResolverInterface $pathResolver)
    {
        parent::__construct(static::COMMAND_NAME);
        $this->evaluatorExecutor = $evaluatorExecutor;
        $this->reportRenderer = $reportRenderer;
        $this->pathResolver = $pathResolver;
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
                static::CHECKERS_OPTION,
                null,
                InputOption::VALUE_OPTIONAL,
                'Run evaluator with specific checkers. Use comma to set multiply checkers',
            )
            ->setHelp('Run evaluator for the current project');
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

        $this->reportRenderer->render($report, $output);

        return $report->isSuccessful() ? static::SUCCESS : static::FAILURE;
    }
}
