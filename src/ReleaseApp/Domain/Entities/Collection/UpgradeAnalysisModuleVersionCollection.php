<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection;

use SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeAnalysisModuleVersion;

class UpgradeAnalysisModuleVersionCollection
{
    /**
     * @var array<\SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeAnalysisModuleVersion>
     */
    protected array $elements = [];

    /**
     * @param array<\SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeAnalysisModuleVersion> $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeAnalysisModuleVersion $element
     *
     * @return void
     */
    public function add(UpgradeAnalysisModuleVersion $element): void
    {
        $this->elements[] = $element;
    }

    /**
     * @return array<\SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeAnalysisModuleVersion>
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return !$this->elements;
    }

    /**
     * @return self
     */
    public function getSecurityFixes(): self
    {
        return new self(
            array_filter(
                $this->elements,
                fn (UpgradeAnalysisModuleVersion $element): bool => $element->isSecurity()
            ),
        );
    }
}
