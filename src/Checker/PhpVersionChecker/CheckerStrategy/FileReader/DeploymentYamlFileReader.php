<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy\FileReader;

use FilesystemIterator;
use GlobIterator;
use Symfony\Component\Yaml\Yaml;

class DeploymentYamlFileReader
{
    /**
     * @param string $globPattern
     *
     * @return iterable<string, array<mixed>>
     */
    public function read(string $globPattern): iterable
    {
        $fileIterator = new GlobIterator($globPattern, FilesystemIterator::CURRENT_AS_PATHNAME);

        /** @var string $filePath */
        foreach ($fileIterator as $filePath) {
            yield $filePath => Yaml::parseFile($filePath);
        }
    }
}
