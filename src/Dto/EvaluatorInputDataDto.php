<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Dto;

class EvaluatorInputDataDto
{
    /**
     * @var string
     */
    protected string $path;

    /**
     * @var array<string>
     */
    protected array $checkerNames;

    /**
     * @var array<string>
     */
    private array $excludedCheckerNames;

    /**
     * @param string $path
     * @param array<string> $checkerNames
     * @param array<string> $excludedCheckerNames
     */
    public function __construct(string $path, array $checkerNames = [], array $excludedCheckerNames = [])
    {
        $this->path = $path;
        $this->checkerNames = $checkerNames;
        $this->excludedCheckerNames = $excludedCheckerNames;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return array<string>
     */
    public function getCheckerNames(): array
    {
        return $this->checkerNames;
    }

    /**
     * @return array<string>
     */
    public function getExcludedCheckerNames(): array
    {
        return $this->excludedCheckerNames;
    }
}
