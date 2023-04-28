<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\PhpVersionChecker;

class CheckerStrategyResponse
{
    /**
     * @var array<string>
     */
    protected array $usedVersions;

    /**
     * @var array<\SprykerSdk\Evaluator\Dto\ViolationDto>
     */
    protected array $violations;

    /**
     * @param array<string> $usedVersions
     * @param array<\SprykerSdk\Evaluator\Dto\ViolationDto> $violations
     */
    public function __construct(array $usedVersions, array $violations)
    {
        $this->usedVersions = $usedVersions;
        $this->violations = $violations;
    }

    /**
     * @return array<string>
     */
    public function getUsedVersions(): array
    {
        return $this->usedVersions;
    }

    /**
     * @return array<\SprykerSdk\Evaluator\Dto\ViolationDto>
     */
    public function getViolations(): array
    {
        return $this->violations;
    }
}
