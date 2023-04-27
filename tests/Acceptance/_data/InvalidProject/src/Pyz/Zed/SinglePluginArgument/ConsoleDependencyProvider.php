<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\InvalidProject\src\Pyz\Zed\SinglePluginArgument;

use Test\Zed\Monitoring\Communication\Plugin\Console\MonitoringConsolePlugin;

class ConsoleDependencyProvider
{
    /**
     * @return \Pyz\Zed\Console\Communication\Plugin\MonitoringConsolePlugin
     */
    public function getMonitoringConsoleMethod(): MonitoringConsolePlugin
    {
        return new MonitoringConsolePlugin(true);
    }
}
