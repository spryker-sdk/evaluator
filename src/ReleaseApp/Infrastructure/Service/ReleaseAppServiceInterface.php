<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Service;

use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse;

interface ReleaseAppServiceInterface
{
    public function getNewReleaseGroups(UpgradeInstructionsRequest $upgradeInstructionsRequest): ReleaseAppResponse;
}
