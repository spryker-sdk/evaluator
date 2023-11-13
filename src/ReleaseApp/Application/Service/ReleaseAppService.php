<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Application\Service;

use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\ReleaseAppClientInterface;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest;
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
     * @param \SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest $upgradeInstructionsRequest
     *
     * @return \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection
     */
    public function getNewReleaseGroupsSortedByReleaseDate(
        UpgradeInstructionsRequest $upgradeInstructionsRequest
    ): UpgradeInstructionsReleaseGroupCollection {
        $releaseGroupCollection = $this->releaseAppClient->getUpgradeInstructions($upgradeInstructionsRequest)->getReleaseGroups()->getOnlyWithReleasedDate();

        return new UpgradeInstructionsReleaseGroupCollection($releaseGroupCollection->getSecurityFixes()->sortByReleasedDate()->toArray());
    }
}
