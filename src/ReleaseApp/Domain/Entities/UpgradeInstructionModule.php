<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Domain\Entities;

use SprykerSdk\Evaluator\ReleaseApp\Domain\Exception\ReleaseAppException;

class UpgradeInstructionModule
{
    /**
     * @var string
     */
    public const VERSION_KEY = 'version';

    /**
     * @var string
     */
    protected const TYPE_KEY = 'type';

    /**
     * @var array<mixed>
     */
    protected array $body;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @param array<mixed> $body
     * @param string $name
     */
    public function __construct(array $body, string $name)
    {
        $this->body = $body;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @throws \SprykerSdk\Evaluator\ReleaseApp\Domain\Exception\ReleaseAppException
     *
     * @return string
     */
    public function getVersion(): string
    {
        if (!array_key_exists(static::VERSION_KEY, $this->body)) {
            throw new ReleaseAppException(sprintf('Key %s not found', static::VERSION_KEY));
        }

        return $this->body[static::VERSION_KEY];
    }

    /**
     * @param string $version
     *
     * @return void
     */
    public function setVersion(string $version): void
    {
        $this->body[static::VERSION_KEY] = $version;
    }

    /**
     * @throws \SprykerSdk\Evaluator\ReleaseApp\Domain\Exception\ReleaseAppException
     *
     * @return string
     */
    public function getType(): string
    {
        if (!array_key_exists(static::TYPE_KEY, $this->body)) {
            throw new ReleaseAppException(sprintf('Key %s not found', static::TYPE_KEY));
        }

        return $this->body[static::TYPE_KEY];
    }

    /**
     * @param string $type
     *
     * @return void
     */
    public function setType(string $type): void
    {
        $this->body[static::TYPE_KEY] = $type;
    }
}
