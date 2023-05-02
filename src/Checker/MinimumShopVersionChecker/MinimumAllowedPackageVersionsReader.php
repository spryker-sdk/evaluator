<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\MinimumShopVersionChecker;

use InvalidArgumentException;

class MinimumAllowedPackageVersionsReader
{
    /**
     * @var string
     */
    protected string $minimumAllowedPackagesFile;

    /**
     * @param string $minimumAllowedPackagesFile
     */
    public function __construct(string $minimumAllowedPackagesFile)
    {
        $this->minimumAllowedPackagesFile = $minimumAllowedPackagesFile;
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @return array<string, string>
     */
    public function getMinimumAllowedPackageVersions(): array
    {
        $content = file_get_contents($this->minimumAllowedPackagesFile);

        if ($content === false) {
            throw new InvalidArgumentException(
                sprintf('Unable to read file %s. Error: %s', $this->minimumAllowedPackagesFile, error_get_last()['message'] ?? '-'),
            );
        }

        return json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
    }
}
