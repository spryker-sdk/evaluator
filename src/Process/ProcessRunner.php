<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Process;

use Symfony\Component\Process\Process;

class ProcessRunner implements ProcessRunnerInterface
{
    /**
     * @param array<string> $command
     *
     * @return \Symfony\Component\Process\Process
     */
    public function run(array $command): Process
    {
        $process = new Process($command);
        $process->setTimeout(static::DEFAULT_PROCESS_TIMEOUT);
        $process->run();

        return $process;
    }
}
