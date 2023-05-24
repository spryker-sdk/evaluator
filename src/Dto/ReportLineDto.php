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
     * @var string
     */
    protected string $docUrl;

    /**
     * @var \SprykerSdk\Evaluator\Dto\DebugInfoDto|null
     */
    private ?DebugInfoDto $debugInfo;

    /**
     * @param string $checkerName
     * @param array<\SprykerSdk\Evaluator\Dto\ViolationDto> $violations
     * @param string $docUrl
     * @param \SprykerSdk\Evaluator\Dto\DebugInfoDto|null $debugInfo
     */
    public function __construct(string $checkerName, array $violations = [], string $docUrl = '', ?DebugInfoDto $debugInfo = null)
    {
        $this->checkerName = $checkerName;
        $this->violations = $violations;
        $this->docUrl = $docUrl;
        $this->debugInfo = $debugInfo;
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
     * @return string
     */
    public function getDocUrl(): string
    {
        return $this->docUrl;
    }

    /**
     * @return \SprykerSdk\Evaluator\Dto\DebugInfoDto|null
     */
    public function getDebugInfo(): ?DebugInfoDto
    {
        return $this->debugInfo;
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return count($this->violations) === 0;
    }
}
