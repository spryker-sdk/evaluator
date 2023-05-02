<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\InvalidProject\MultidimensionalArray\Application2;

use ValidProject\src\Pyz\Zed\Application\MerchantMerchantProductSearchWritePublisherPlugin;
use ValidProject\src\Pyz\Zed\Application\MerchantProductSearchWritePublisherPlugin;

class ApplicationDependencyProvider
{
    /**
     * @return array
     */
    protected function getPublisherPlugins(): array
    {
        $productSearchPlugins = [
            new MerchantMerchantProductSearchWritePublisherPlugin(),
            new MerchantProductSearchWritePublisherPlugin(),
        ];

        return array_merge(
            [
                new MerchantMerchantProductSearchWritePublisherPlugin(),
                new MerchantProductSearchWritePublisherPlugin(),
                PublishAndSynchronizeHealthCheckConfig::PUBLISH_PUBLISH_AND_SYNCHRONIZE_HEALTH_CHECK => [
                    PublishAndSynchronizeHealthCheckConfig::PUBLISH_PUBLISH_AND_SYNCHRONIZE_HEALTH_CHECK => [
                        new MerchantMerchantProductSearchWritePublisherPlugin(),
                    ],
                ],
            ],
            $productSearchPlugins,
        );
    }
}
