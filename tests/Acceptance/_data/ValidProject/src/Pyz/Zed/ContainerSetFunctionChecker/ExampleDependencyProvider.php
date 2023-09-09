<?php

declare(strict_types=1);

namespace SprykerSdkTest\ValidProject\Pyz\Zed\ContainerSetFunctionChecker;

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
            return $this->getProductSalePageWidgetPlugins();
        });

        return $container;
    }

    /**
     * @return array<string>
     */
    protected function getProductSalePageWidgetPlugins(): array
    {
        return [];
    }
}
