<?php

declare(strict_types=1);

namespace InvalidProject\src\Pyz\Zed\ContainerSetFunctionChecker;

class ExampleDependencyProvider
{
    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addProductSalePageWidgetPlugins($container): Container
    {
        $container->set(static::PYZ_PLUGIN_PRODUCT_SALE_PAGE_WIDGETS, function () {
            return [
                new ProductSalePageWidgetPlugin(),
            ];
        });

        return $container;
    }

}
