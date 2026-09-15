<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Dto;

class CheckerInputDataDto
{
    protected string $path;

    /**
     * @var array<mixed>
     */
    protected array $configuration = [];

    /**
     * @param array<mixed> $configuration
     */
    public function __construct(string $path, array $configuration = [])
    {
        $this->path = $path;
        $this->configuration = $configuration;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return array<mixed>
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }
}
