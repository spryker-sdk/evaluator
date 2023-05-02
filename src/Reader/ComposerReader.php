<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Reader;

use InvalidArgumentException;
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
     * @var \SprykerSdk\Evaluator\Resolver\PathResolverInterface
     */
    protected PathResolverInterface $pathResolver;

    /**
     * @param \SprykerSdk\Evaluator\Resolver\PathResolverInterface $pathResolver
     */
    public function __construct(PathResolverInterface $pathResolver)
    {
        $this->pathResolver = $pathResolver;
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
     * @param string $filePath
     *
     * @throws \InvalidArgumentException
     *
     * @return array<mixed>
     */
    protected function readFile(string $filePath): array
    {
        $content = file_get_contents($filePath);

        if ($content === false) {
            throw new InvalidArgumentException(sprintf('Unable to read file %s. Error: %s', $filePath, error_get_last()['message'] ?? '-'));
        }

        return json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
    }
}
