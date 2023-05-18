<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Finder;

use Symfony\Component\Finder\Finder;

class SourceFinder implements SourceFinderInterface
{
    /**
     * @var string
     */
    protected const NOT_PREFIX = '!';

    /**
     * @var array<string>
     */
    protected const DEFAULT_EXCLUDE_DIRS = [
        'src' . DIRECTORY_SEPARATOR . 'Generated',
        'vendor',
    ];

    /**
     * @param array<string> $pattern
     * @param array<string> $paths
     * @param array<string> $exclude
     *
     * @return \Symfony\Component\Finder\Finder
     */
    public function find(array $pattern, array $paths, array $exclude = self::DEFAULT_EXCLUDE_DIRS): Finder
    {
        $name = [];
        $notName = [];
        foreach ($pattern as $item) {
            if (strpos($item, static::NOT_PREFIX) !== 0) {
                $name[] = $item;

                continue;
            }

            $notName[] = ltrim($item, static::NOT_PREFIX);
        }

        $finder = Finder::create();
        $finder->in($paths);
        if ($name) {
            $finder->name($name);
        }
        if ($notName) {
            $finder->notName($notName);
        }
        $finder->exclude($exclude);

        return $finder;
    }
}
