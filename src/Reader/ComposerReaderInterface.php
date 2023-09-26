<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Reader;

interface ComposerReaderInterface
{
    /**
     * @return array<mixed>
     */
    public function getComposerData(): array;

    /**
     * @return array<mixed>
     */
    public function getComposerLockData(): array;

    /**
     * @return array<string, string>
     */
    public function getComposerRequirePackages(): array;

    /**
     * @param string $packageName
     *
     * @return string|null
     */
    public function getPackageVersion(string $packageName): ?string;

    /**
     * @return string
     */
    public function getProjectName(): string;
}
