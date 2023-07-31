<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Mapper;

use SprykerSdk\Evaluator\ReleaseApp\Application\Configuration\ConfigurationProviderInterface;
use SprykerSdk\Evaluator\ReleaseApp\Application\Configuration\ReleaseAppConstant;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeInstructionMeta;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeInstructionsReleaseGroup;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;

class ReleaseGroupDtoCollectionMapper
{
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Application\Configuration\ConfigurationProviderInterface $configurationProvider
     */
    public function __construct(ConfigurationProviderInterface $configurationProvider)
    {
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection $releaseGroupCollection
     *
     * @return \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection
     */
    public function mapReleaseGroupTransferCollection(
        UpgradeInstructionsReleaseGroupCollection $releaseGroupCollection
    ): ReleaseGroupDtoCollection {
        $dataProviderReleaseGroupCollection = new ReleaseGroupDtoCollection();

        foreach ($releaseGroupCollection->toArray() as $releaseGroup) {
            $dataProviderReleaseGroupCollection->add($this->mapReleaseGroupDto($releaseGroup));
        }

        return $dataProviderReleaseGroupCollection;
    }

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeInstructionsReleaseGroup $releaseGroup
     *
     * @return \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection
     */
    public function mapReleaseGroupDtoIntoCollection(UpgradeInstructionsReleaseGroup $releaseGroup): ReleaseGroupDtoCollection
    {
        $dataProviderReleaseGroupCollection = new ReleaseGroupDtoCollection();
        $dataProviderReleaseGroupCollection->add($this->mapReleaseGroupDto($releaseGroup));

        return $dataProviderReleaseGroupCollection;
    }

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeInstructionsReleaseGroup $releaseGroup
     *
     * @return \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto
     */
    protected function mapReleaseGroupDto(UpgradeInstructionsReleaseGroup $releaseGroup): ReleaseGroupDto
    {
        $dataProviderReleaseGroup = new ReleaseGroupDto(
            $releaseGroup->getName(),
            $this->buildModuleTransferCollection($releaseGroup),
            $releaseGroup->hasProjectChanges(),
            $this->getReleaseGroupLink($releaseGroup->getId()),
        );
        $dataProviderReleaseGroup->setHasConflict(
            $releaseGroup->getMeta() && $releaseGroup->getMeta()->getConflict()->count(),
        );
        $dataProviderReleaseGroup->setJiraIssue($releaseGroup->getJiraIssue());
        $dataProviderReleaseGroup->setJiraIssueLink($releaseGroup->getJiraIssueLink());
        $dataProviderReleaseGroup->setIsSecurity($releaseGroup->isSecurity());

        return $dataProviderReleaseGroup;
    }

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeInstructionsReleaseGroup $releaseGroup
     *
     * @return \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection
     */
    protected function buildModuleTransferCollection(UpgradeInstructionsReleaseGroup $releaseGroup): ModuleDtoCollection
    {
        $releaseGroupModuleCollection = $releaseGroup->getModuleCollection();
        if ($releaseGroup->getMeta()) {
            $releaseGroupModuleCollection = $this->applyMeta($releaseGroupModuleCollection, $releaseGroup->getMeta());
        }

        $dataProviderModuleCollection = new ModuleDtoCollection();
        foreach ($releaseGroupModuleCollection->toArray() as $module) {
            $dataProviderModule = new ModuleDto($module->getName(), $module->getVersion(), $module->getType());
            $dataProviderModuleCollection->add($dataProviderModule);
        }

        return $dataProviderModuleCollection;
    }

    /**
     * @param int $id
     *
     * @return string
     */
    protected function getReleaseGroupLink(int $id): string
    {
        return sprintf(ReleaseAppConstant::RELEASE_GROUP_LINK_PATTERN, $this->configurationProvider->getReleaseAppUrl(), $id);
    }

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection $moduleCollection
     * @param \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeInstructionMeta $meta
     *
     * @return \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection
     */
    protected function applyMeta(
        UpgradeInstructionModuleCollection $moduleCollection,
        UpgradeInstructionMeta $meta
    ): UpgradeInstructionModuleCollection {
        foreach ($meta->getInclude()->toArray() as $moduleInclude) {
            $module = $moduleCollection->getByName($moduleInclude->getName());
            if ($module) {
                $module->setVersion($moduleInclude->getVersion());

                continue;
            }

            $moduleInclude->setType(ReleaseAppConstant::MODULE_TYPE_PATCH);
            $moduleCollection->add($moduleInclude);
        }
        foreach ($meta->getExclude()->toArray() as $moduleExclude) {
            $moduleCollection->deleteByName($moduleExclude->getName());
        }

        return $moduleCollection;
    }
}
