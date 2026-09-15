<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\NpmChecker;

use SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface;

class NpmInstallationValidator
{
    private ProcessRunnerServiceInterface $processRunner;

    public function __construct(ProcessRunnerServiceInterface $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    public function isNpmInstalled(): bool
    {
        return $this->processRunner->run(['npm', '-v'])->isSuccessful();
    }
}
