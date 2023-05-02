<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Parser;

use PhpParser\Node;

interface NodeFinderInterface
{
    /**
     * Find all nodes satisfying a filter callback.
     *
     * @param \PhpParser\Node|array<\PhpParser\Node> $nodes Single node or array of nodes to search in
     * @param callable $filter Filter callback: function(Node $node) : bool
     *
     * @return array<\PhpParser\Node> Found nodes satisfying the filter callback
     */
    public function find($nodes, callable $filter): array;

    /**
     * Find all nodes that are instances of a certain class.
     *
     * @param \PhpParser\Node|array<\PhpParser\Node> $nodes Single node or array of nodes to search in
     * @param string $class Class name
     *
     * @return array<\PhpParser\Node> Found nodes (all instances of $class)
     */
    public function findInstanceOf($nodes, string $class): array;

    /**
     * Find first node satisfying a filter callback.
     *
     * @param \PhpParser\Node|array<\PhpParser\Node> $nodes Single node or array of nodes to search in
     * @param callable $filter Filter callback: function(Node $node) : bool
     *
     * @return \PhpParser\Node|null Found node (or null if none found)
     */
    public function findFirst($nodes, callable $filter): ?Node;

    /**
     * Find first node that is an instance of a certain class.
     *
     * @param \PhpParser\Node|array<\PhpParser\Node> $nodes Single node or array of nodes to search in
     * @param string $class Class name
     *
     * @return \PhpParser\Node|null Found node, which is an instance of $class (or null if none found)
     */
    public function findFirstInstanceOf($nodes, string $class): ?Node;
}
