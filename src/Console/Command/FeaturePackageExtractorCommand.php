<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Console\Command;

use SprykerSdk\Evaluator\Extractor\FeaturePackagesExtractor\FeaturePackagesExtractorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FeaturePackageExtractorCommand extends Command
{
    /**
     * @var string
     */
    public const COMMAND_NAME = 'extract-feature-packages';

    /**
     * @var \SprykerSdk\Evaluator\Extractor\FeaturePackagesExtractor\FeaturePackagesExtractorInterface
     */
    private FeaturePackagesExtractorInterface $featurePackagesExtractor;

    /**
     * @param \SprykerSdk\Evaluator\Extractor\FeaturePackagesExtractor\FeaturePackagesExtractorInterface $featurePackagesExtractor
     */
    public function __construct(FeaturePackagesExtractorInterface $featurePackagesExtractor)
    {
        parent::__construct(static::COMMAND_NAME);
        $this->featurePackagesExtractor = $featurePackagesExtractor;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setHelp('Execution with github auth: GITHUB_AUTH=<token> bin/console extract-feature-packages')
            ->setDescription('Spryker feature package extractor');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Start extracting feature packages...');

        $this->featurePackagesExtractor->extract();

        return static::SUCCESS;
    }
}
