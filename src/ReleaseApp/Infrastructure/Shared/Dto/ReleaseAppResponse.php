<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto;

use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;

class ReleaseAppResponse
{
    protected ReleaseGroupDtoCollection $releaseGroupCollection;

    public function __construct(ReleaseGroupDtoCollection $releaseGroupCollection)
    {
        $this->releaseGroupCollection = $releaseGroupCollection;
    }

    public function getReleaseGroupCollection(): ReleaseGroupDtoCollection
    {
        return $this->releaseGroupCollection;
    }
}
