<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker;

interface CheckerRegistryInterface
{
    /**
     * @return array<\SprykerSdk\Evaluator\Checker\CheckerInterface>
     */
    public function getAllCheckers(): array;
}
