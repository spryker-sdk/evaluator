<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Report\Builder;

use DateTimeImmutable;
use SprykerSdk\Evaluator\Dto\ReportDto as EvaluatorReportDto;
use SprykerSdk\Evaluator\Report\Dto\ReportDto;
use SprykerSdk\Evaluator\Report\Dto\ReportMetadataDto;
use SprykerSdk\Evaluator\Report\Dto\ReportPayloadDto;
use Symfony\Component\Uid\Uuid;

class ReportDtoBuilder implements ReportDtoBuilderInterface
{
    /**
     * @var string
     */
    public const REPORT_NAME = 'static_evaluator_report';

    /**
     * @var string
     */
    public const REPORT_SCOPE = 'Static_Evaluator';

    /**
     * @var int
     */
    public const REPORT_VERSION = 1;

    /**
     * @var string
     */
    protected string $sourceCodeProvider;

    /**
     * @var string
     */
    protected string $appEnv;

    /**
     * @var string
     */
    protected string $projectId;

    /**
     * @var string
     */
    protected string $repositoryName;

    /**
     * @var string
     */
    protected string $organizationName;

    /**
     * @param string $sourceCodeProvider
     * @param string $appEnv
     * @param string $projectId
     * @param string $repositoryName
     * @param string $organizationName
     */
    public function __construct(
        string $sourceCodeProvider,
        string $appEnv,
        string $projectId,
        string $repositoryName,
        string $organizationName
    ) {
        $this->sourceCodeProvider = $sourceCodeProvider;
        $this->appEnv = $appEnv;
        $this->projectId = $projectId;
        $this->repositoryName = $repositoryName;
        $this->organizationName = $organizationName;
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\ReportDto $evaluatorReportDto
     *
     * @return \SprykerSdk\Evaluator\Report\Dto\ReportDto
     */
    public function buildReportDto(EvaluatorReportDto $evaluatorReportDto): ReportDto
    {
        return new ReportDto(
            static::REPORT_NAME,
            static::REPORT_VERSION,
            static::REPORT_SCOPE,
            new DateTimeImmutable(),
            $this->createReportPayload($evaluatorReportDto),
            $this->createReportMetadata(),
        );
    }

    /**
     * @return \SprykerSdk\Evaluator\Report\Dto\ReportMetadataDto
     */
    protected function createReportMetadata(): ReportMetadataDto
    {
        return new ReportMetadataDto(
            $this->organizationName,
            $this->repositoryName,
            $this->projectId,
            $this->sourceCodeProvider,
            $this->appEnv,
            Uuid::v4()->toRfc4122(),
        );
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\ReportDto $evaluatorReportDto
     *
     * @return \SprykerSdk\Evaluator\Report\Dto\ReportPayloadDto
     */
    protected function createReportPayload(EvaluatorReportDto $evaluatorReportDto): ReportPayloadDto
    {
        return new ReportPayloadDto($evaluatorReportDto);
    }
}
