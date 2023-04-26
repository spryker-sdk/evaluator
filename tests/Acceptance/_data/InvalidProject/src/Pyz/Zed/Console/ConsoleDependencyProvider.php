<?php

declare(strict_types=1);

namespace InvalidProject\src\Pyz\Zed\Console;

use Spryker\Zed\Console\Communication\Plugin\ConsoleLogPlugin;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Monitoring\Communication\Plugin\Console\MonitoringConsolePlugin;

class ConsoleDependencyProvider
{
    /**
     * @var bool
     */
    protected const IS_DEV = false;

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return array
     */
    public function getEventSubscriber(Container $container): array
    {
        $eventSubscriber = parent::getEventSubscriber($container);

        if (!static::IS_DEV) {
            $eventSubscriber[] = new ConsoleLogPlugin();
            $eventSubscriber[] = new MonitoringConsolePlugin();
        }

        return $eventSubscriber;
    }
}
