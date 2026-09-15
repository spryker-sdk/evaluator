<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Service;

use SprykerSdk\Evaluator\ReleaseApp\Application\Service\ReleaseAppService as ApplicationReleaseAppService;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Mapper\ReleaseGroupDtoCollectionMapper;

class ReleaseAppService implements ReleaseAppServiceInterface
{
    protected ApplicationReleaseAppService $releaseApp;

    protected ReleaseGroupDtoCollectionMapper $releaseGroupDtoCollectionMapper;

    public function __construct(
        ApplicationReleaseAppService $releaseApp,
        ReleaseGroupDtoCollectionMapper $releaseGroupDtoCollectionMapper
    ) {
        $this->releaseApp = $releaseApp;
        $this->releaseGroupDtoCollectionMapper = $releaseGroupDtoCollectionMapper;
    }

    public function getNewReleaseGroups(UpgradeInstructionsRequest $upgradeInstructionsRequest): ReleaseAppResponse
    {
        $releaseGroupCollection = $this->releaseGroupDtoCollectionMapper->mapReleaseGroupTransferCollection(
            $this->releaseApp->getNewReleaseGroupsSortedByReleaseDate($upgradeInstructionsRequest),
        );

        return new ReleaseAppResponse($releaseGroupCollection);
    }
}
