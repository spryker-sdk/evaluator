<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Application\Service;

use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\ReleaseAppClientInterface;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeAnalysisModuleVersionCollection;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection;

class ReleaseAppService implements ReleaseAppServiceInterface
{
    /**
     * @var \SprykerSdk\Evaluator\ReleaseApp\Domain\Client\ReleaseAppClientInterface
     */
    protected ReleaseAppClientInterface $releaseAppClient;

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Domain\Client\ReleaseAppClientInterface $releaseAppClient
     */
    public function __construct(ReleaseAppClientInterface $releaseAppClient)
    {
        $this->releaseAppClient = $releaseAppClient;
    }

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest $upgradeAnalysisRequest
     *
     * @return \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection
     */
    public function getNewReleaseGroupsSortedByReleaseDate(
        UpgradeAnalysisRequest $upgradeAnalysisRequest
    ): UpgradeInstructionsReleaseGroupCollection {
        $moduleVersionCollection = $this->getModuleVersionCollection($upgradeAnalysisRequest)->getSecurityFixes();

        $releaseGroupCollection = $this->getReleaseGroupCollection($moduleVersionCollection)
            ->getOnlyWithReleasedDate()->getSecurityFixes()->sortByReleasedDate();

        return $releaseGroupCollection;
    }

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeAnalysisModuleVersionCollection $moduleVersionCollection
     *
     * @return \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection
     */
    protected function getReleaseGroupCollection(
        UpgradeAnalysisModuleVersionCollection $moduleVersionCollection
    ): UpgradeInstructionsReleaseGroupCollection {
        $releaseGroupCollection = new UpgradeInstructionsReleaseGroupCollection();

        foreach ($moduleVersionCollection->toArray() as $moduleVersion) {
            $request = new UpgradeInstructionsRequest($moduleVersion->getId());
            $response = $this->releaseAppClient->getUpgradeInstructions($request);
            $releaseGroupCollection->add($response->getReleaseGroup());
        }

        return $releaseGroupCollection;
    }

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest $request
     *
     * @return \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeAnalysisModuleVersionCollection
     */
    protected function getModuleVersionCollection(
        UpgradeAnalysisRequest $request
    ): UpgradeAnalysisModuleVersionCollection {
        $response = $this->releaseAppClient->getUpgradeAnalysis($request);

        return $response->getModuleCollection()
            ->getModulesWithVersions()
            ->getModuleVersions();
    }
}
