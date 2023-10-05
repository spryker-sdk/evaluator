<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker;

abstract class AbstractChecker implements CheckerInterface
{
    /**
     * @var array<mixed>
     */
    protected array $config = [];

    /**
     * @return bool
     */
    public function isApplicable(): bool
    {
        return true;
    }

    /**
     * @param array<mixed> $config
     *
     * @return void
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }
}
