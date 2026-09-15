<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Report\Dto;

class ReportMetadataDto
{
    protected string $organizationName;

    protected string $repositoryName;

    protected string $projectId;

    protected string $sourceCodeProvider;

    protected string $appEnv;

    protected string $reportId;

    public function __construct(
        string $organizationName,
        string $repositoryName,
        string $projectId,
        string $sourceCodeProvider,
        string $appEnv,
        string $reportId
    ) {
        $this->organizationName = $organizationName;
        $this->repositoryName = $repositoryName;
        $this->projectId = $projectId;
        $this->sourceCodeProvider = $sourceCodeProvider;
        $this->appEnv = $appEnv;
        $this->reportId = $reportId;
    }

    public function getOrganizationName(): string
    {
        return $this->organizationName;
    }

    public function getRepositoryName(): string
    {
        return $this->repositoryName;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getSourceCodeProvider(): string
    {
        return $this->sourceCodeProvider;
    }

    public function getAppEnv(): string
    {
        return $this->appEnv;
    }

    public function getReportId(): string
    {
        return $this->reportId;
    }
}
