<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Zed\Category;

use Spryker\Zed\Category\CategoryDependencyProvider as SprykerDependencyProvider;
use Spryker\Zed\CategoryNavigationConnector\Communication\Plugin\UpdateNavigationRelationPlugin;
use Spryker\Zed\CmsBlockCategoryConnector\Communication\Plugin\Category\CmsBlockCategoryCategoryRelationPlugin;
use Spryker\Zed\ProductCategory\Communication\Plugin\UpdateProductCategoryRelationPlugin;

class TestDependencyProvider extends SprykerDependencyProvider
{
    /**
     * @return array<\Spryker\Zed\CategoryExtension\Dependency\Plugin\CategoryRelationUpdatePluginInterface>
     */
    protected function getRelationUpdatePluginStack(): array
    {
        return [
            /**
             * Restrictions:
             * - after {@link \Spryker\Zed\CategoryNavigationConnector\Communication\Plugin\InvalidPlagin}
             * - afterrrr {@link \Spryker\Zed\CmsBlockCategoryConnector\Communication\Plugin\Category\CmsBlockCategoryCategoryRelationPlugin} Call it always after ProductOfferReferenceStrategyPlugin and MerchantProductProductOfferReferenceStrategyPlugin.
             */
            new UpdateProductCategoryRelationPlugin(),
            new UpdateNavigationRelationPlugin(),
            /**
             * Restrictions:
             * OtherBlock:
             * - some {@link \Spryker\Zed\CategoryNavigationConnector\Communication\Plugin\InvalidPlagin}
             */
            new CmsBlockCategoryCategoryRelationPlugin(),
        ];
    }

    /**
     * @return array<\Spryker\Zed\CategoryExtension\Dependency\Plugin\CategoryTransferExpanderPluginInterface>
     */
    protected function getCategoryPostReadPlugins(): array
    {
        return [
            /**
             * Restrictions:
             * - after {@linkk \Spryker\Zed\CategoryNavigationConnector\Communication\Plugin\UpdateNavigationRelationPlugin}
             */
            new CategoryImageSetExpanderPlugin(),
        ];
    }
}
