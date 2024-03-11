<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection;

use RuntimeException;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeInstructionsReleaseGroup;

class UpgradeInstructionsReleaseGroupCollection
{
    /**
     * @var array<\SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeInstructionsReleaseGroup>
     */
    protected array $elements = [];

    /**
     * @param array<\SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeInstructionsReleaseGroup> $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeInstructionsReleaseGroup $element
     *
     * @return void
     */
    public function add(UpgradeInstructionsReleaseGroup $element): void
    {
        $this->elements[] = $element;
    }

    /**
     * @return array<\SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeInstructionsReleaseGroup>
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
     * @param \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection|self $collectionToMerge
     *
     * @return void
     */
    public function addCollection(self $collectionToMerge): void
    {
        $this->elements = array_merge($this->elements, $collectionToMerge->toArray());
    }

    /**
     * @return self
     */
    public function sortByReleasedDate(): self
    {
        $sortData = [];

        foreach ($this->elements as $releaseGroup) {
            $timestamp = $releaseGroup->getReleased()->getTimestamp();
            $sortData[$timestamp] = $releaseGroup;
        }

        ksort($sortData);

        return new self(array_values($sortData));
    }

    /**
     * @return self
     */
    public function getSecurityFixes(): self
    {
        return new self(
            array_filter(
                $this->elements,
                fn (UpgradeInstructionsReleaseGroup $releaseGroup): bool => $releaseGroup->isSecurity(),
            ),
        );
    }

    /**
     * @return self
     */
    public function getNonSecurityFixes(): self
    {
        return new self(
            array_filter(
                $this->elements,
                fn (UpgradeInstructionsReleaseGroup $releaseGroup): bool => !$releaseGroup->isSecurity(),
            ),
        );
    }

    /**
     * @return self
     */
    public function getOnlyWithReleasedDate(): self
    {
        $result = new self();

        foreach ($this->elements as $releaseGroup) {
            try {
                $dateTime = $releaseGroup->getReleased();
            } catch (RuntimeException $exception) {
                $dateTime = null;
            }

            if ($dateTime) {
                $result->add($releaseGroup);
            }
        }

        return $result;
    }
}
