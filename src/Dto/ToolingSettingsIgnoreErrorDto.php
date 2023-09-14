<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Dto;

class ToolingSettingsIgnoreErrorDto
{
    /**
     * @var array<string>
     */
    protected array $messageRegexps;

    /**
     * @var string|null
     */
    protected ?string $checkerName;

    /**
     * @param array<string> $messageRegexps
     * @param string|null $checkerName
     */
    public function __construct(array $messageRegexps, ?string $checkerName = null)
    {
        $this->messageRegexps = $messageRegexps;
        $this->checkerName = $checkerName;
    }

    /**
     * @return array<string>
     */
    public function getMessageRegexps(): array
    {
        return $this->messageRegexps;
    }

    /**
     * @return string|null
     */
    public function getCheckerName(): ?string
    {
        return $this->checkerName;
    }
}
