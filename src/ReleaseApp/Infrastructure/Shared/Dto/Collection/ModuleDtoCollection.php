<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\Collection;

use SprykerSdk\Evaluator\ReleaseApp\Application\Configuration\ReleaseAppConstant;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;

class ModuleDtoCollection
{
    /**
     * @var array<\SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto>
     */
    protected array $elements = [];

    /**
     * @param array<\SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto> $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto $element
     *
     * @return void
     */
    public function add(ModuleDto $element): void
    {
        $this->elements[] = $element;
    }

    /**
     * @return array<\SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto>
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
     * @param \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection|self $collectionToMerge
     *
     * @return void
     */
    public function addCollection(self $collectionToMerge): void
    {
        $this->elements = array_merge($this->elements, $collectionToMerge->toArray());
    }

    /**
     * @return array<\SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto>
     */
    public function getMajors(): array
    {
        return array_filter(
            $this->elements,
            static fn (ModuleDto $module): bool => $module->getVersionType() === ReleaseAppConstant::MODULE_TYPE_MAJOR,
        );
    }

    /**
     * @return int
     */
    public function getMajorAmount(): int
    {
        $result = 0;
        foreach ($this->elements as $module) {
            if ($module->getVersionType() === ReleaseAppConstant::MODULE_TYPE_MAJOR) {
                $result++;
            }
        }

        return $result;
    }

    /**
     * @return int
     */
    public function getMinorAmount(): int
    {
        $result = 0;
        foreach ($this->elements as $module) {
            if ($module->getVersionType() === ReleaseAppConstant::MODULE_TYPE_MINOR) {
                $result++;
            }
        }

        return $result;
    }

    /**
     * @return int
     */
    public function getPatchAmount(): int
    {
        $result = 0;
        foreach ($this->elements as $module) {
            if ($module->getVersionType() === ReleaseAppConstant::MODULE_TYPE_PATCH) {
                $result++;
            }
        }

        return $result;
    }
}
