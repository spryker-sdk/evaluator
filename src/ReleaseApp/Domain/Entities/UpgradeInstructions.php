<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Domain\Entities;

use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Response\Response;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Exception\ReleaseAppException;

class UpgradeInstructions extends Response
{
    /**
     * @var string
     */
    protected const RELEASE_GROUPS_KEY = 'release_groups';

    /**
     * @throws \SprykerSdk\Evaluator\ReleaseApp\Domain\Exception\ReleaseAppException
     *
     * @return \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection
     */
    public function getReleaseGroups(): UpgradeInstructionsReleaseGroupCollection
    {
        $bodyArray = $this->getBody();

        if (!$bodyArray) {
            throw new ReleaseAppException('Response body not found');
        }
        $releaseGroupCollection = new UpgradeInstructionsReleaseGroupCollection();

        foreach ($bodyArray[static::RELEASE_GROUPS_KEY] as $releaseGroup) {
            $releaseGroupCollection->add(new UpgradeInstructionsReleaseGroup($releaseGroup));
        }

        return $releaseGroupCollection;
    }
}
