<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Console\ReportRenderer;

use InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractOutputReport implements ReportRendererInterface
{
    /**
     * @var string
     */
    protected const EXTENSION = 'txt';

    /**
     * @var string|null
     */
    protected ?string $filePath = null;

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
     *
     * @return void
     */
    public function setFile(string $filePath): void
    {
        $filePath = str_replace('*', static::EXTENSION, $filePath);
        $this->createGitignore($filePath);
        $this->filePath = $filePath;
    }

    /**
     * @param string $content
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    protected function saveToFile(string $content): void
    {
        if ($this->filePath === null) {
            throw new InvalidArgumentException(sprintf('File `%s` does not set', $this->filePath));
        }
        $this->filesystem->dumpFile($this->filePath, $content);
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
