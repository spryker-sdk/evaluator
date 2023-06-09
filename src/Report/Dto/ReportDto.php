<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Report\Dto;

use DateTimeImmutable;

class ReportDto
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var int
     */
    protected int $version;

    /**
     * @var string
     */
    protected string $scope;

    /**
     * @var \DateTimeImmutable
     */
    protected DateTimeImmutable $createdAt;

    /**
     * @var \SprykerSdk\Evaluator\Report\Dto\ReportPayloadDto
     */
    protected ReportPayloadDto $payload;

    /**
     * @var \SprykerSdk\Evaluator\Report\Dto\ReportMetadataDto
     */
    protected ReportMetadataDto $metadata;

    /**
     * @param string $name
     * @param int $version
     * @param string $scope
     * @param \DateTimeImmutable $createdAt
     * @param \SprykerSdk\Evaluator\Report\Dto\ReportPayloadDto $payload
     * @param \SprykerSdk\Evaluator\Report\Dto\ReportMetadataDto $metadata
     */
    public function __construct(
        string $name,
        int $version,
        string $scope,
        DateTimeImmutable $createdAt,
        ReportPayloadDto $payload,
        ReportMetadataDto $metadata
    ) {
        $this->name = $name;
        $this->version = $version;
        $this->scope = $scope;
        $this->createdAt = $createdAt;
        $this->payload = $payload;
        $this->metadata = $metadata;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return \SprykerSdk\Evaluator\Report\Dto\ReportPayloadDto
     */
    public function getPayload(): ReportPayloadDto
    {
        return $this->payload;
    }

    /**
     * @return \SprykerSdk\Evaluator\Report\Dto\ReportMetadataDto
     */
    public function getMetadata(): ReportMetadataDto
    {
        return $this->metadata;
    }
}
