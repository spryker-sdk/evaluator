<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Service;

use SprykerSdk\Evaluator\ReleaseApp\Application\Service\ReleaseAppService as ApplicationReleaseAppService;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Mapper\ReleaseGroupDtoCollectionMapper;

class ReleaseAppService implements ReleaseAppServiceInterface
{
    /**
     * @var \SprykerSdk\Evaluator\ReleaseApp\Application\Service\ReleaseAppService
     */
    protected ApplicationReleaseAppService $releaseApp;

    /**
     * @var \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Mapper\ReleaseGroupDtoCollectionMapper
     */
    protected ReleaseGroupDtoCollectionMapper $releaseGroupDtoCollectionMapper;

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Application\Service\ReleaseAppService $releaseApp
     * @param \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Mapper\ReleaseGroupDtoCollectionMapper $releaseGroupDtoCollectionMapper
     */
    public function __construct(
        ApplicationReleaseAppService $releaseApp,
        ReleaseGroupDtoCollectionMapper $releaseGroupDtoCollectionMapper
    ) {
        $this->releaseApp = $releaseApp;
        $this->releaseGroupDtoCollectionMapper = $releaseGroupDtoCollectionMapper;
    }

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest $upgradeAnalysisRequest
     *
     * @return \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse
     */
    public function getNewSecurityReleaseGroups(UpgradeAnalysisRequest $upgradeAnalysisRequest): ReleaseAppResponse
    {
        $releaseGroupCollection = $this->releaseGroupDtoCollectionMapper->mapReleaseGroupTransferCollection(
            $this->releaseApp->getNewReleaseGroupsSortedByReleaseDate($upgradeAnalysisRequest),
        );

        return new ReleaseAppResponse($releaseGroupCollection);
    }
}
