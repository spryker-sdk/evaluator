<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Finder;

use PhpParser\Node\Stmt\Class_;

interface StatementFinderInterface
{
    /**
     * @param array<\PhpParser\Node> $syntaxTree
     *
     * @throws \RuntimeException
     *
     * @return \PhpParser\Node\Stmt\Class_
     */
    public function findClassStatement(array $syntaxTree): Class_;
}
