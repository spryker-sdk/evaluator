<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Finder;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeFinder;
use RuntimeException;

class StatementFinder implements StatementFinderInterface
{
    /**
     * @param array<\PhpParser\Node> $syntaxTree
     *
     * @throws \RuntimeException
     *
     * @return \PhpParser\Node\Stmt\Class_
     */
    public function findClassStatement(array $syntaxTree): Class_
    {
        /** @var \PhpParser\Node\Stmt\Class_|null $node */
        $node = (new NodeFinder())->findFirst($syntaxTree, function (Node $node) {
            return $node instanceof Class_;
        });

        if (!$node) {
            throw new RuntimeException('Can\'t get class statement from syntax tree');
        }

        return $node;
    }
}
