<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Reader;

use SprykerSdk\Evaluator\Filesystem\Filesystem;
use SprykerSdk\Evaluator\Resolver\PathResolverInterface;

class ComposerReader implements ComposerReaderInterface
{
    /**
     * @var string
     */
    protected const COMPOSER_FILE_NAME = 'composer.json';

    /**
     * @var string
     */
    protected const COMPOSER_LOCK_FILE_NAME = 'composer.lock';

    /**
     * @string
     *
     * @var string
     */
    protected const REQUIRE_KEY = 'require';

    /**
     * @var string
     */
    protected const PACKAGES_KEY = 'packages';

    /**
     * @var string
     */
    protected const PACKAGES_DEV_KEY = 'packages-dev';

    /**
     * @var string
     */
    protected const NAME_KEY = 'name';

    /**
     * @var string
     */
    protected const VERSION_KEY = 'version';

    /**
     * @var \SprykerSdk\Evaluator\Resolver\PathResolverInterface
     */
    protected PathResolverInterface $pathResolver;

    /**
     * @var \SprykerSdk\Evaluator\Filesystem\Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @param \SprykerSdk\Evaluator\Resolver\PathResolverInterface $pathResolver
     * @param \SprykerSdk\Evaluator\Filesystem\Filesystem $filesystem
     */
    public function __construct(PathResolverInterface $pathResolver, Filesystem $filesystem)
    {
        $this->pathResolver = $pathResolver;
        $this->filesystem = $filesystem;
    }

    /**
     * @return array<mixed>
     */
    public function getComposerData(): array
    {
        return $this->readFile($this->pathResolver->resolvePath() . DIRECTORY_SEPARATOR . static::COMPOSER_FILE_NAME);
    }

    /**
     * @return array<mixed>
     */
    public function getComposerLockData(): array
    {
        return $this->readFile($this->pathResolver->resolvePath() . DIRECTORY_SEPARATOR . static::COMPOSER_LOCK_FILE_NAME);
    }

    /**
     * @return array<string, string>
     */
    public function getComposerRequirePackages(): array
    {
        return $this->getComposerData()[static::REQUIRE_KEY] ?? [];
    }

    /**
     * @param string $filePath
     *
     * @return array<mixed>
     */
    protected function readFile(string $filePath): array
    {
        $content = $this->filesystem->readFile($filePath);

        return json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
    }

    /**
     * @param string $packageName
     *
     * @return string|null
     */
    public function getPackageVersion(string $packageName): ?string
    {
        $composerLock = $this->getComposerLockData();

        foreach ($composerLock[static::PACKAGES_KEY] as $package) {
            if ($package[static::NAME_KEY] == $packageName) {
                return $package[static::VERSION_KEY];
            }
        }

        foreach ($composerLock[static::PACKAGES_DEV_KEY] as $package) {
            if ($package[static::NAME_KEY] == $packageName) {
                return $package[static::VERSION_KEY];
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function getProjectName(): string
    {
        $composerJsonContent = $this->getComposerData();

        return $composerJsonContent[static::NAME_KEY];
    }

    /**
     * @return array<mixed, array<string, mixed>>
     */
    public function getInstalledPackages(): array
    {
        $installedPackages = [];

        $composerLock = $this->getComposerLockData();

        foreach ($composerLock[static::PACKAGES_KEY] as $package) {
            $installedPackages[$package[static::NAME_KEY]] = $package;
        }

        foreach ($composerLock[static::PACKAGES_DEV_KEY] as $package) {
            $installedPackages[$package[static::NAME_KEY]] = $package;
        }

        return $installedPackages;
    }
}
