<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Domain\Entities;

use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Response\Response;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeAnalysisModuleCollection;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Exception\ReleaseAppException;

class UpgradeAnalysis extends Response
{
    /**
     * @var string
     */
    protected const MODULES_KEY = 'modules';

    /**
     * @var \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeAnalysisModuleCollection|null
     */
    protected ?UpgradeAnalysisModuleCollection $moduleCollection = null;

    /**
     * @return \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\Collection\UpgradeAnalysisModuleCollection
     */
    public function getModuleCollection(): UpgradeAnalysisModuleCollection
    {
        if ($this->moduleCollection) {
            return $this->moduleCollection;
        }

        $moduleList = [];
        foreach ($this->getModules() as $moduleData) {
            $moduleList[] = new UpgradeAnalysisModule($moduleData);
        }
        $this->moduleCollection = new UpgradeAnalysisModuleCollection($moduleList);

        return $this->moduleCollection;
    }

    /**
     * @throws \SprykerSdk\Evaluator\ReleaseApp\Domain\Exception\ReleaseAppException
     *
     * @return array<mixed>
     */
    protected function getModules(): array
    {
        $body = $this->getBody();

        if (!$body) {
            throw new ReleaseAppException('Response body not found');
        }

        if (!array_key_exists(static::MODULES_KEY, $body)) {
            throw new ReleaseAppException('Key modules not found');
        }

        return $body[static::MODULES_KEY];
    }
}
