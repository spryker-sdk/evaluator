<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\MultidimensionalArrayChecker\NestingStructure;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;

class AssignArrayNestingStructure extends AbstractNestingStructure
{
    /**
     * @param \PhpParser\Node\Stmt $stmt
     *
     * @return bool
     */
    public function isApplicable(Stmt $stmt): bool
    {
        return $stmt instanceof Expression && $stmt->expr instanceof Assign && $stmt->expr->expr instanceof Array_;
    }

    /**
     * @param \PhpParser\Node\Stmt $stmt
     *
     * @return int
     */
    public function getDepth(Stmt $stmt): int
    {
        if (!$this->isApplicable($stmt)) {
        }

        return $this->arrayDepth(
            $stmt->expr->expr, // @phpstan-ignore-line
        );
    }
}
