<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Parser;

use PhpParser\Node;
use PhpParser\NodeFinder as PhpParserNodeFinder;

/**
 * This proxy is needed for fixing return type hints
 */
class NodeFinder extends PhpParserNodeFinder implements NodeFinderInterface
{
    /**
     * @param \PhpParser\Node|array<\PhpParser\Node> $nodes
     *
     * @return array<\PhpParser\Node>
     */
    public function find($nodes, callable $filter): array
    {
        return parent::find($nodes, $filter);
    }

    /**
     * @template TNode of Node
     *
     * @param \PhpParser\Node|array<\PhpParser\Node> $nodes
     * @param class-string<TNode> $class
     *
     * @return array<TNode>
     */
    public function findInstanceOf($nodes, string $class): array
    {
        return parent::findInstanceOf($nodes, $class);
    }

    /**
     * @param \PhpParser\Node|array<\PhpParser\Node> $nodes
     */
    public function findFirst($nodes, callable $filter): ?Node
    {
        return parent::findFirst($nodes, $filter);
    }

    /**
     * @template TNode of Node
     *
     * @param \PhpParser\Node|array<\PhpParser\Node> $nodes
     * @param class-string<TNode> $class
     *
     * @return TNode|null
     */
    public function findFirstInstanceOf($nodes, string $class): ?Node
    {
        return parent::findFirstInstanceOf($nodes, $class);
    }
}
