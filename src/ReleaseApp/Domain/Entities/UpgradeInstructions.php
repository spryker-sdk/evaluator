<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Domain\Entities;

use RuntimeException;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Response\Response;

class UpgradeInstructions extends Response
{
    /**
     * @var string
     */
    protected const RELEASE_GROUP_KEY = 'release_group';

    /**
     * @throws \RuntimeException
     *
     * @return \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeInstructionsReleaseGroup
     */
    public function getReleaseGroup(): UpgradeInstructionsReleaseGroup
    {
        $bodyArray = $this->getBody();

        if (!$bodyArray) {
            throw new RuntimeException('Response body not found');
        }

        return new UpgradeInstructionsReleaseGroup($bodyArray[static::RELEASE_GROUP_KEY]);
    }
}
