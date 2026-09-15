<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Reader;

use SprykerSdk\Evaluator\Resolver\PathResolverInterface;
use SprykerSdk\Utils\Infrastructure\Service\Filesystem;
use Symfony\Component\Yaml\Yaml;

class ToolingSettingsFileReader implements ToolingSettingsReaderInterface
{
    protected PathResolverInterface $pathResolver;

    protected string $toolingFile;

    protected Filesystem $filesystem;

    public function __construct(PathResolverInterface $pathResolver, string $toolingFile, Filesystem $filesystem)
    {
        $this->pathResolver = $pathResolver;
        $this->toolingFile = $toolingFile;
        $this->filesystem = $filesystem;
    }

    /**
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

        return Yaml::parse($toolingSettingsString);
    }

    protected function getToolingFilePath(): string
    {
        return rtrim($this->pathResolver->getProjectDir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $this->toolingFile;
    }
}
