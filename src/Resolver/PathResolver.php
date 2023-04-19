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
    protected string $projectDir;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @param string $projectDir
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(string $projectDir, Filesystem $filesystem)
    {
        $this->projectDir = $projectDir;
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $path
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function resolvePath(string $path = ''): string
    {
        $path = trim($path);

        if (!$path) {
            return $this->projectDir;
        }

        $fullPath = $this->projectDir . DIRECTORY_SEPARATOR . trim($path, DIRECTORY_SEPARATOR);

        if (!$this->filesystem->exists([$fullPath])) {
            throw new InvalidArgumentException(sprintf('File or directory `%s` does not exist', $fullPath));
        }

        return $fullPath;
    }
}
