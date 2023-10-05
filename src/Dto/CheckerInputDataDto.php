<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Dto;

class CheckerInputDataDto
{
    /**
     * @var string
     */
    protected string $path;

    /**
     * @var array<mixed>
     */
    protected array $configuration = [];

    /**
     * @param string $path
     * @param array<mixed> $configuration
     */
    public function __construct(string $path, array $configuration = [])
    {
        $this->path = $path;
        $this->configuration = $configuration;
    }

    /**
     * @return string
     */
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
