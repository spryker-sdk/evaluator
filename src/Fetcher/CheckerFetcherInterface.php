<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Fetcher;

use SprykerSdk\Evaluator\Dto\EvaluatorInputDataDto;

interface CheckerFetcherInterface
{
    /**
     * @param \SprykerSdk\Evaluator\Dto\EvaluatorInputDataDto $inputData
     *
     * @return array<\SprykerSdk\Evaluator\Checker\CheckerInterface>
     */
    public function getCheckersFilteredByInputData(EvaluatorInputDataDto $inputData): array;
}
