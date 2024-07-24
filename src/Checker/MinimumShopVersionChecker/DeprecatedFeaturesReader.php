<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\MinimumShopVersionChecker;

use InvalidArgumentException;

class DeprecatedFeaturesReader
{
    /**
     * @var string
     */
    protected string $deprecatedFeaturesFile;

    /**
     * @param string $deprecatedFeaturesFile
     */
    public function __construct(string $deprecatedFeaturesFile)
    {
        $this->deprecatedFeaturesFile = $deprecatedFeaturesFile;
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @return array<string, string>
     */
    public function getDeprecatedFeatures(): array
    {
        $content = file_get_contents($this->deprecatedFeaturesFile);

        if ($content === false) {
            throw new InvalidArgumentException(
                sprintf('Unable to read file %s. Error: %s', $this->deprecatedFeaturesFile, error_get_last()['message'] ?? '-'),
            );
        }

        return json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
    }
}
