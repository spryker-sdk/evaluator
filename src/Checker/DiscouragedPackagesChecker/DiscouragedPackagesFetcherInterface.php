<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\DiscouragedPackagesChecker;

interface DiscouragedPackagesFetcherInterface
{
    /**
     * @param array<string> $packageNames
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     *
     * @return array<\SprykerSdk\Evaluator\Checker\DiscouragedPackagesChecker\DiscouragedPackageDto>
     */
    public function fetchDiscouragedPackagesByPackageNames(array $packageNames): array;
}
