<?php

namespace SprykerSdkTest\ValidProject\Pyz\Zed\DeadClass\Model;

use Spryker\Zed\DeadClass as SprykerDeadClass;

class DeadClass extends SprykerDeadClass
{
    /**
     * @return \SprykerSdkTest\ValidProject\Pyz\Zed\DeadClass\Model\DeadClassSameNamespace
     */
    public function createDeadClassSameNamespace(): DeadClassSameNamespace
    {
        return new DeadClassSameNamespace();
    }

    /**
     * @return string
     */
    public function getDeadClassSameNamespaceName(): string
    {
        return DeadClassSameNamespace::class;
    }
}
