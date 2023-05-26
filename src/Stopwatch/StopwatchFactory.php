<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Stopwatch;

use Symfony\Component\Stopwatch\Stopwatch;

class StopwatchFactory
{
    /**
     * @return \Symfony\Component\Stopwatch\Stopwatch
     */
    public function getStopWatch(): Stopwatch
    {
        return new Stopwatch();
    }
}
