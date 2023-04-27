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
        return Finder::create()->in($paths)->name($pattern)->exclude($exclude);
    }
}
