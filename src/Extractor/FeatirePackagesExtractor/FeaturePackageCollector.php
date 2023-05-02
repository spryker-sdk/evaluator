<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Extractor\FeatirePackagesExtractor;

class FeaturePackageCollector
{
    /**
     * @var string
     */
    protected const SPRYKER_PACKAGE_PREFIX = 'spryker';

    /**
     * @param array<string, string> $collectedPackages
     * @param array<string, string> $newPackages
     *
     * @return array<string, string>
     */
    public function collect(array $collectedPackages, array $newPackages): array
    {
        foreach ($newPackages as $package => $version) {
            if (strpos($package, static::SPRYKER_PACKAGE_PREFIX) !== 0) {
                continue;
            }

            $version = (string)preg_replace('/[^\d.]/', '', $version);

            if (isset($collectedPackages[$package]) && version_compare($collectedPackages[$package], $version, '<=')) {
                continue;
            }

            $collectedPackages[$package] = $version;
        }

        return $collectedPackages;
    }
}
