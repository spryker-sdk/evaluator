<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Domain\Entities;

use SprykerSdk\Evaluator\Helper\TextCaseHelper;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection;

class UpgradeInstructionMeta
{
    /**
     * @var string
     */
    protected const INCLUDE_KEY = 'include';

    /**
     * @var string
     */
    protected const EXCLUDE_KEY = 'exclude';

    /**
     * @var string
     */
    protected const CONFLICT_KEY = 'conflict';

    /**
     * @var \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection
     */
    protected UpgradeInstructionModuleCollection $includeModuleCollection;

    /**
     * @var \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection
     */
    protected UpgradeInstructionModuleCollection $excludeModuleCollection;

    /**
     * @var \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection
     */
    protected UpgradeInstructionModuleCollection $conflictModuleCollection;

    /**
     * @var array<mixed>
     */
    protected array $body;

    /**
     * @param array<mixed> $body
     */
    public function __construct(array $body)
    {
        $this->body = $body;
        $this->includeModuleCollection = new UpgradeInstructionModuleCollection(
            $this->getModuleListByKey(static::INCLUDE_KEY),
        );
        $this->excludeModuleCollection = new UpgradeInstructionModuleCollection(
            $this->getModuleListByKey(static::EXCLUDE_KEY),
        );
        $this->conflictModuleCollection = new UpgradeInstructionModuleCollection(
            $this->getModuleListByKey(static::CONFLICT_KEY),
        );
    }

    /**
     * @return \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection
     */
    public function getInclude(): UpgradeInstructionModuleCollection
    {
        return $this->includeModuleCollection;
    }

    /**
     * @return \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection
     */
    public function getExclude(): UpgradeInstructionModuleCollection
    {
        return $this->excludeModuleCollection;
    }

    /**
     * @return \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection
     */
    public function getConflict(): UpgradeInstructionModuleCollection
    {
        return $this->conflictModuleCollection;
    }

    /**
     * @param string $key
     *
     * @return array<\SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeInstructionModule>
     */
    protected function getModuleListByKey(string $key): array
    {
        $list = [];
        if (!isset($this->body[$key])) {
            return $list;
        }

        foreach ($this->body[$key] as $name => $version) {
            $list[] = new UpgradeInstructionModule(
                [UpgradeInstructionModule::VERSION_KEY => $version],
                TextCaseHelper::packageCamelCaseToDash($name),
            );
        }

        return $list;
    }
}
