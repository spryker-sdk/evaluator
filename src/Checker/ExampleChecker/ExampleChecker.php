<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\ExampleChecker;

use SprykerSdk\Evaluator\Checker\CheckerInterface;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;

class ExampleChecker implements CheckerInterface
{
    /**
     * @var string
     */
    protected const NAME = 'EXAMPLE_CHECKER';

    /**
     * @param \SprykerSdk\Evaluator\Dto\CheckerInputDataDto $inputData
     *
     * @return array<\SprykerSdk\Evaluator\Dto\ViolationDto>
     */
    public function check(CheckerInputDataDto $inputData): array
    {
        return [new ViolationDto(sprintf('Path: %s', $inputData->getPath()), 'someFile.php')];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
    }
}
