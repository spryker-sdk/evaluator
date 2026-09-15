<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto;

class ModuleDto
{
    protected string $name;

    protected string $version;

    protected string $versionType;

    public function __construct(string $name, string $version, string $versionType)
    {
        $this->name = $name;
        $this->version = $version;
        $this->versionType = $versionType;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getVersionType(): string
    {
        return $this->versionType;
    }
}
