<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Finder;

use Symfony\Component\Finder\Finder;

interface SourceFinderInterface
{
    /**
     * @param array<string> $pattern
     * @param array<string> $paths
     * @param array<string> $exclude
     */
    public function find(array $pattern, array $paths, array $exclude = []): Finder;
}
