<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\MultidimensionalArrayChecker\NestingStructure;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Return_;

class ReturnArrayNestingStructure extends AbstractNestingStructure
{
    public function isApplicable(Stmt $stmt): bool
    {
        return $stmt instanceof Return_ && $stmt->expr instanceof Array_;
    }

    public function getDepth(Stmt $stmt): int
    {
        return $this->arrayDepth(
            $stmt->expr, // @phpstan-ignore-line
        );
    }
}
