<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Resolver;

use InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;

class PathResolver implements PathResolverInterface
{
    /**
     * @var string
     */
    protected string $projectDirEnv;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @param string $projectDirEnv
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(string $projectDirEnv, Filesystem $filesystem)
    {
        $this->projectDirEnv = $projectDirEnv;
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $relativePath
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function resolvePath(string $relativePath = ''): string
    {
        $fullPath = $this->createPath($relativePath);

        if (!is_dir($fullPath) || !$this->filesystem->exists([$fullPath])) {
            throw new InvalidArgumentException(sprintf('Directory `%s` does not exist', $fullPath));
        }

        return $fullPath;
    }

    /**
     * @param string $relativePath
     *
     * @return string
     */
    public function createPath(string $relativePath = ''): string
    {
        if ($this->filesystem->isAbsolutePath($relativePath)) {
            return $relativePath;
        }

        $relativePath = trim($relativePath, " \t\n\r\0\x0B" . DIRECTORY_SEPARATOR);

        $projectDir = $this->getProjectDir();

         return $relativePath
            ? $projectDir . DIRECTORY_SEPARATOR . $relativePath
            : $projectDir;
    }

    /**
     * @return string
     */
    public function getProjectDir(): string
    {
        return $this->projectDirEnv ?: (string)getcwd();
    }
}
