<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Console\ReportRenderer;

use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractOutputReport implements ReportRendererInterface
{
    /**
     * @var string
     */
    protected const EXTENSION = 'txt';

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $filePath
     * @param string $content
     *
     * @return void
     */
    protected function saveToFile(string $filePath, string $content): void
    {
        $filePath = str_replace('*', static::EXTENSION, $filePath);
        $this->createGitignore($filePath);

        $this->filesystem->dumpFile($filePath, $content);
    }

    /**
     * @param string $fileName
     *
     * @return void
     */
    protected function createGitignore(string $fileName): void
    {
        $reportDir = dirname($fileName);
        $ignoreRules = [
            '*',
            '!.gitignore',
            '!' . basename($fileName),
        ];

        if (realpath($reportDir) !== realpath('.')) {
            $this->filesystem->dumpFile(
                sprintf('%s/.gitignore', $reportDir),
                implode(PHP_EOL, $ignoreRules),
            );
        }
    }
}
