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
     * @param callable $filter
     *
     * @return array<\PhpParser\Node>
     */
    public function find($nodes, callable $filter): array
    {
        return parent::find($nodes, $filter);
    }

    /**
     * @param \PhpParser\Node|array<\PhpParser\Node> $nodes
     * @param string $class
     *
     * @return array<\PhpParser\Node>
     */
    public function findInstanceOf($nodes, string $class): array
    {
        return parent::findInstanceOf($nodes, $class);
    }

    /**
     * @param \PhpParser\Node|array<\PhpParser\Node> $nodes
     * @param callable $filter
     *
     * @return \PhpParser\Node|null
     */
    public function findFirst($nodes, callable $filter): ?Node
    {
        return parent::findFirst($nodes, $filter);
    }

    /**
     * @param \PhpParser\Node|array<\PhpParser\Node> $nodes
     * @param string $class
     *
     * @return \PhpParser\Node|null
     */
    public function findFirstInstanceOf($nodes, string $class): ?Node
    {
        return parent::findFirstInstanceOf($nodes, $class);
    }
}
