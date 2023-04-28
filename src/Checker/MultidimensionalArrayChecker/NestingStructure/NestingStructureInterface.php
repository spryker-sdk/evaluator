<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\MultidimensionalArrayChecker\NestingStructure;

use PhpParser\Node\Stmt;

interface NestingStructureInterface
{
    /**
     * @param \PhpParser\Node\Stmt $stmt
     *
     * @return bool
     */
    public function isApplicable(Stmt $stmt): bool;

    /**
     * @param \PhpParser\Node\Stmt $stmt
     *
     * @return int
     */
    public function getDepth(Stmt $stmt): int;
}
