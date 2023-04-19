<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker;

use SprykerSdk\Evaluator\Dto\CheckerInputDto;

interface CheckerInterface
{
    /**
     * @param \SprykerSdk\Evaluator\Dto\CheckerInputDto $input
     *
     * @return array<\SprykerSdk\Evaluator\Dto\ViolationDto>
     */
    public function check(CheckerInputDto $input): array;

    /**
     * @return string
     */
    public function getName(): string;
}
