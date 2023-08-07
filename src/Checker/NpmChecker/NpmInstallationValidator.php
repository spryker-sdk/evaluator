<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\NpmChecker;

use SprykerSdk\Evaluator\Process\ProcessRunnerInterface;

class NpmInstallationValidator
{
    /**
     * @var \SprykerSdk\Evaluator\Process\ProcessRunnerInterface
     */
    private ProcessRunnerInterface $processRunner;

    /**
     * @param \SprykerSdk\Evaluator\Process\ProcessRunnerInterface $processRunner
     */
    public function __construct(ProcessRunnerInterface $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * @return bool
     */
    public function isNpmInstalled(): bool
    {
        return $this->processRunner->run(['npm', '-v'])->isSuccessful();
    }
}
