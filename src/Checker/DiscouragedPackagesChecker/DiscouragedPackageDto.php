<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\DiscouragedPackagesChecker;

class DiscouragedPackageDto
{
    protected string $packageName;

    protected string $reason;

    public function __construct(string $packageName, string $reason)
    {
        $this->packageName = $packageName;
        $this->reason = $reason;
    }

    public function getPackageName(): string
    {
        return $this->packageName;
    }

    public function getReason(): string
    {
        return $this->reason;
    }
}
