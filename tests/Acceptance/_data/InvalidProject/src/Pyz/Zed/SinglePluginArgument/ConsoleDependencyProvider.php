<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\InvalidProject\Pyz\Zed\SinglePluginArgument;

use Spryker\Zed\Monitoring\Communication\Plugin\Console\MonitoringConsolePlugin;
use stdClass;

class ConsoleDependencyProvider
{
    /**
     * @return \Spryker\Zed\Console\Communication\Plugin\MonitoringConsolePlugin
     */
    public function getMonitoringConsoleMethod(): MonitoringConsolePlugin
    {
        $variable = new stdClass();

        return new MonitoringConsolePlugin($variable);
    }
}
