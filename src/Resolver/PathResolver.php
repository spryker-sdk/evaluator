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
     * @var string|null
     */
    protected ?string $projectDirEnv;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @param string|null $projectDirEnv
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(?string $projectDirEnv, Filesystem $filesystem)
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
        $relativePath = trim($relativePath);

        $projectDir = $this->getProjectDir();

        $fullPath = $relativePath
            ? $projectDir . DIRECTORY_SEPARATOR . trim($relativePath, DIRECTORY_SEPARATOR)
            : $projectDir;

        if (!$this->filesystem->exists([$fullPath])) {
            throw new InvalidArgumentException(sprintf('File or directory `%s` does not exist', $fullPath));
        }

        return $fullPath;
    }

    /**
     * @return string
     */
    public function getProjectDir(): string
    {
        return $this->projectDirEnv ?? (string)getcwd();
    }
}
