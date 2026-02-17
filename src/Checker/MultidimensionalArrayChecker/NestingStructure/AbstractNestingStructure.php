<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\MultidimensionalArrayChecker\NestingStructure;

use PhpParser\Node\Expr\Array_;

abstract class AbstractNestingStructure implements NestingStructureInterface
{
    /**
     * @param \PhpParser\Node\Expr\Array_ $array
     *
     * @return int
     */
    protected function arrayDepth(Array_ $array): int
    {
        $maxDepth = 1;

        foreach ($array->items as $arrayItem) {
            if ($arrayItem && $arrayItem->value instanceof Array_) { // @phpstan-ignore booleanAnd.leftAlwaysTrue
                $depth = $this->arrayDepth($arrayItem->value);
                $depth++;

                if ($depth > $maxDepth) {
                    $maxDepth = $depth;
                }
            }
        }

        return $maxDepth;
    }
}
