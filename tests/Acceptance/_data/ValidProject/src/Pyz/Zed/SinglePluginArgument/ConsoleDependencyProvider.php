<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\ValidProject\src\Pyz\Zed\SinglePluginArgument;

use Spryker\Zed\Console\Communication\Plugin\ConsoleLogPlugin;
use stdClass;
use Test\Zed\Monitoring\Communication\Plugin\Console\MonitoringConsolePlugin;

class ConsoleDependencyProvider
{
    /**
     * @return \Spryker\Zed\Console\Communication\Plugin\ConsoleLogPlugin
     */
    public function getConsoleLog(): ConsoleLogPlugin
    {
        return new ConsoleLogPlugin();
    }

    /**
     * @evaluator-skip-single-plugin-argument
     *
     * @return \Spryker\Zed\Console\Communication\Plugin\ConsoleLogPlugin
     */
    public function getConsoleLogSkip(): ConsoleLogPlugin
    {
        $f = new stdClass();

        return new ConsoleLogPlugin($f);
    }

    /**
     * @return \Spryker\Zed\Console\Communication\Plugin\ConsoleLogPlugin
     */
    public function getConsoleLogPugin(): ConsoleLogPlugin
    {
        return new ConsoleLogPlugin(null, 12.1, 12, ConsoleLogPlugin::CONST1 . ConsoleLogPlugin::CONST2 . 'test', 'string', new MonitoringConsolePlugin());
    }

    /**
     * @return \Pyz\Zed\Console\Communication\Plugin\MonitoringConsolePlugin
     */
    public function getMonitoringConsole(): MonitoringConsolePlugin
    {
        return new MonitoringConsolePlugin();
    }

    /**
     * @return \Pyz\Zed\Console\Communication\Plugin\MonitoringConsolePlugin
     */
    public function getMonitoringConsoleMethod(): MonitoringConsolePlugin
    {
        return new MonitoringConsolePlugin(true);
    }
}
