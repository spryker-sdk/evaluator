<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Dto;

class ReportLineDto
{
    /**
     * @var string
     */
    protected string $checkerName;

    /**
     * @var array<\SprykerSdk\Evaluator\Dto\ViolationDto>
     */
    protected array $violations;

    /**
     * @param string $checkerName
     * @param array<\SprykerSdk\Evaluator\Dto\ViolationDto> $violations
     */
    public function __construct(string $checkerName, array $violations = [])
    {
        $this->checkerName = $checkerName;
        $this->violations = $violations;
    }

    /**
     * @return string
     */
    public function getCheckerName(): string
    {
        return $this->checkerName;
    }

    /**
     * @return array<\SprykerSdk\Evaluator\Dto\ViolationDto>
     */
    public function getViolations(): array
    {
        return $this->violations;
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return count($this->violations) === 0;
    }
}
