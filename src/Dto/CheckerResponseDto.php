<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Dto;

class CheckerResponseDto
{
    /**
     * @var array<\SprykerSdk\Evaluator\Dto\ViolationDto>
     */
    protected array $violations;

    /**
     * @var string
     */
    protected string $docUrl;

    /**
     * @param array<\SprykerSdk\Evaluator\Dto\ViolationDto> $violations
     * @param string $docUrl
     */
    public function __construct(array $violations, string $docUrl = '')
    {
        $this->violations = $violations;
        $this->docUrl = $docUrl;
    }

    /**
     * @return array<\SprykerSdk\Evaluator\Dto\ViolationDto>
     */
    public function getViolations(): array
    {
        return $this->violations;
    }

    /**
     * @return string
     */
    public function getDocUrl(): string
    {
        return $this->docUrl;
    }
}
