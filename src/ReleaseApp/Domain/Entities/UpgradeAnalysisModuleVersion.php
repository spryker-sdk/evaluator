<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Domain\Entities;

use DateTime;
use DateTimeInterface;
use RuntimeException;
use SprykerSdk\Evaluator\ReleaseApp\Application\Configuration\ReleaseAppConstant;

class UpgradeAnalysisModuleVersion
{
    /**
     * @var string
     */
    protected const ID_KEY = 'id';

    /**
     * @var string
     */
    protected const NAME_KEY = 'name';

    /**
     * @var string
     */
    protected const SECURITY_KEY = 'is_security';

    /**
     * @var string
     */
    protected const CREATED_KEY = 'created';

    /**
     * @var array<mixed>
     */
    protected array $body;

    /**
     * @param array<mixed> $bodyArray
     */
    public function __construct(array $bodyArray)
    {
        $this->body = $bodyArray;
    }

    /**
     * @throws \RuntimeException
     *
     * @return int
     */
    public function getId(): int
    {
        if (!array_key_exists(static::ID_KEY, $this->body)) {
            throw new RuntimeException(sprintf('Key %s not found', static::ID_KEY));
        }

        return $this->body[static::ID_KEY];
    }

    /**
     * @throws \RuntimeException
     *
     * @return int
     */
    public function getName(): int
    {
        if (!array_key_exists(static::NAME_KEY, $this->body)) {
            throw new RuntimeException(sprintf('Key %s not found', static::NAME_KEY));
        }

        return $this->body[static::NAME_KEY];
    }

    /**
     * @return bool
     */
    public function isSecurity(): bool
    {
        if (!array_key_exists(static::SECURITY_KEY, $this->body)) {
            return false;
        }

        return $this->body[static::SECURITY_KEY];
    }

    /**
     * @throws \RuntimeException
     *
     * @return \DateTimeInterface
     */
    public function getCreated(): DateTimeInterface
    {
        $dataTime = DateTime::createFromFormat(
            ReleaseAppConstant::RESPONSE_DATA_TIME_FORMAT,
            $this->body[static::CREATED_KEY],
        );

        if (!$dataTime) {
            $message = sprintf('%s %s', 'Invalid datatime format:', $this->body[static::CREATED_KEY]);

            throw new RuntimeException($message);
        }

        return $dataTime;
    }
}
