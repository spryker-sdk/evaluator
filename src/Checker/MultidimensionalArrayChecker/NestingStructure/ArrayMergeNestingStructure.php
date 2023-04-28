<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\MultidimensionalArrayChecker\NestingStructure;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Return_;

class ArrayMergeNestingStructure extends AbstractNestingStructure
{
    /**
     * @param \PhpParser\Node\Stmt $stmt
     *
     * @return bool
     */
    public function isApplicable(Stmt $stmt): bool
    {
        return $stmt instanceof Return_ &&
            $stmt->expr instanceof FuncCall &&
            $stmt->expr->name instanceof Name &&
            $stmt->expr->name->toString() === 'array_merge';
    }

    /**
     * @param \PhpParser\Node\Stmt $stmt
     *
     * @return int
     */
    public function getDepth(Stmt $stmt): int
    {
        foreach ($stmt->expr->args as $arg) { // @phpstan-ignore-line
            if (
                $arg instanceof Arg &&
                $arg->value instanceof Array_
            ) {
                return $this->arrayDepth($arg->value);
            }
        }

        return 0;
    }
}
