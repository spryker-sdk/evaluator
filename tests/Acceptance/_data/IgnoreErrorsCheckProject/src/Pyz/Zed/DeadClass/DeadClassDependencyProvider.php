<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\InvalidProject\Pyz\Zed\SinglePluginArgument;

use SprykerSdkTest\InvalidProject\Pyz\Zed\DeadClass\DeadClassOne;
use Spryker\Pyz\DeadClassDependencyProvider as SprykerDeadClassDependencyProvider;

class DeadClassDependencyProvider extends SprykerDeadClassDependencyProvider
{
    /**
     * @return \SprykerSdkTest\InvalidProject\Pyz\Zed\DeadClass\DeadClass
     */
    public function getMonitoringConsoleMethod(): DeadClass
    {
        return new DeadClassOne();
    }
}
