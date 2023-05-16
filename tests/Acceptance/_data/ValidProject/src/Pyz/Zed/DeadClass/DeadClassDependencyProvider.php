<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\ValidProject\Pyz\Zed\DeadClass;

use SprykerSdkTest\ValidProject\Pyz\Zed\DeadClass\Model\DeadClass;
use SprykerSdkTest\ValidProject\Pyz\Zed\DeadClass\Model\DeadClassPlugin;

class DeadClassDependencyProvider
{
    /**
     * @return \SprykerSdkTest\InvalidProject\Pyz\Zed\DeadClass\DeadClass
     */
    public function getDeadClass(): DeadClass
    {
        return new DeadClass();
    }

    /**
     * @return \SprykerSdkTest\ValidProject\Pyz\Zed\DeadClass\Model\DeadClassPlugin
     */
    public function getDeadClassPlugin(): DeadClassPlugin
    {
        return new DeadClassPlugin();
    }
}
