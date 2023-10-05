<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Dto;

class CheckerConfigDto
{
    /**
     * @var string
     */
    protected string $checkerName;

    /**
     * @var array <mixed>
     */
    protected array $config = [];

    /**
     * @param string $checkerName
     * @param array<mixed> $config
     */
    public function __construct(string $checkerName, array $config)
    {
        $this->checkerName = $checkerName;
        $this->config = $config;
    }

    /**
     * @return string|null
     */
    public function getCheckerName(): ?string
    {
        return $this->checkerName;
    }

    /**
     * @return array<mixed>
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
