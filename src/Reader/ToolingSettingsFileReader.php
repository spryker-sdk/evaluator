<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Reader;

use InvalidArgumentException;
use SprykerSdk\Evaluator\Filesystem\Filesystem;
use SprykerSdk\Evaluator\Resolver\PathResolverInterface;
use Symfony\Component\Yaml\Yaml;

class ToolingSettingsFileReader implements ToolingSettingsReaderInterface
{
    /**
     * @var \SprykerSdk\Evaluator\Resolver\PathResolverInterface
     */
    protected PathResolverInterface $pathResolver;

    /**
     * @var string
     */
    protected string $toolingFile;

    /**
     * @var \SprykerSdk\Evaluator\Filesystem\Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @param \SprykerSdk\Evaluator\Resolver\PathResolverInterface $pathResolver
     * @param string $toolingFile
     * @param \SprykerSdk\Evaluator\Filesystem\Filesystem $filesystem
     */
    public function __construct(PathResolverInterface $pathResolver, string $toolingFile, Filesystem $filesystem)
    {
        $this->pathResolver = $pathResolver;
        $this->toolingFile = $toolingFile;
        $this->filesystem = $filesystem;
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @return array<mixed>
     */
    public function readFromFile(): array
    {
        $toolingSettingsFilePath = $this->getToolingFilePath();

        if (!$this->filesystem->exists($toolingSettingsFilePath)) {
            return [];
        }

        $toolingSettingsString = trim($this->filesystem->readFile($toolingSettingsFilePath));

        if ($toolingSettingsString === '') {
            return [];
        }

        $yaml = Yaml::parse($toolingSettingsString);

        if (!is_array($yaml)) {
            throw new InvalidArgumentException(sprintf('Invalid yaml format in "%s"', $toolingSettingsFilePath));
        }

        return $yaml;
    }

    /**
     * @return string
     */
    protected function getToolingFilePath(): string
    {
        return rtrim($this->pathResolver->getProjectDir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $this->toolingFile;
    }
}
