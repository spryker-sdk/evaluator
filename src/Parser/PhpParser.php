<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Parser;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;

class PhpParser implements PhpParserInterface
{
    /**
     * @var \PhpParser\Parser
     */
    protected Parser $parser;

    /**
     * @param \PhpParser\ParserFactory $parserFactory
     */
    public function __construct(ParserFactory $parserFactory)
    {
        $this->parser = $parserFactory->create(ParserFactory::PREFER_PHP7);
    }

    /**
     * @param string $path
     *
     * @return array<\PhpParser\Node>
     */
    public function parse(string $path): array
    {
        if (!file_exists($path)) {
            return [];
        }

        $originalAst = (array)$this->parser->parse((string)file_get_contents($path));

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NameResolver());

        return $nodeTraverser->traverse($originalAst);
    }
}
