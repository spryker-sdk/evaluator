<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Application\Service;

use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection;

interface ReleaseAppServiceInterface
{
    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest $upgradeAnalysisRequest
     *
     * @return \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection
     */
    public function getNewReleaseGroupsSortedByReleaseDate(UpgradeAnalysisRequest $upgradeAnalysisRequest): UpgradeInstructionsReleaseGroupCollection;
}
