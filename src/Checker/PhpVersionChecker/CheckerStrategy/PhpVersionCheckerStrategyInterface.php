<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy;

use SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategyResponse;

interface PhpVersionCheckerStrategyInterface
{
    /**
     * @param array<string> $allowedPhpVersions
     *
     * @return \SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategyResponse
     */
    public function check(array $allowedPhpVersions): CheckerStrategyResponse;

    /**
     * String of target php version check
     *
     * @return string
     */
    public function getTarget(): string;
}
