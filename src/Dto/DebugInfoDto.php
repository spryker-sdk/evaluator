<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Dto;

class DebugInfoDto
{
    /**
     * @var int
     */
    private int $durationInMs;

    /**
     * @var int
     */
    private int $memoryInBytes;

    /**
     * @param int $durationInMs
     * @param int $memoryInBytes
     */
    public function __construct(int $durationInMs, int $memoryInBytes)
    {
        $this->durationInMs = $durationInMs;
        $this->memoryInBytes = $memoryInBytes;
    }

    /**
     * @return int
     */
    public function getDurationInMs(): int
    {
        return $this->durationInMs;
    }

    /**
     * @return int
     */
    public function getMemoryInBytes(): int
    {
        return $this->memoryInBytes;
    }
}
