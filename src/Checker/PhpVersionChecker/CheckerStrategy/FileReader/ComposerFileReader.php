<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy\FileReader;

use InvalidArgumentException;

class ComposerFileReader
{
    /**
     * @param string $composerFile
     *
     * @throws \InvalidArgumentException
     *
     * @return array<mixed>
     */
    public function read(string $composerFile): array
    {
        if (!is_file($composerFile)) {
            throw new InvalidArgumentException(sprintf('File "%s" does not exist', $composerFile));
        }

        $composerFileContent = file_get_contents($composerFile);

        if ($composerFileContent === false) {
            throw new InvalidArgumentException(sprintf('Unable to read file "%s"', $composerFile));
        }

        return json_decode($composerFileContent, true, 512, JSON_THROW_ON_ERROR);
    }
}
