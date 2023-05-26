<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Report\Builder;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Dto\ReportDto;
use SprykerSdk\Evaluator\Dto\ReportLineDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;
use SprykerSdk\Evaluator\Report\Builder\ReportDtoBuilder;

/**
 * @group SprykerSdkTest
 * @group Evaluator
 * @group Unit
 * @group Report
 * @group Builder
 * @group ReportDtoBuilderTest
 */
class ReportDtoBuilderTest extends TestCase
{
    /**
     * @var string
     */
    protected const ORGANIZATION_NAME = 'vendor';

    /**
     * @var string
     */
    protected const REPOSITORY_NAME = 'suite';

    /**
     * @var string
     */
    protected const PROJECT_ID = 'project_id';

    /**
     * @var string
     */
    protected const SOURCE_CODE_PROVIDER = 'github';

    /**
     * @var string
     */
    protected const APP_ENV = 'CI';

    /**
     * @var string
     */
    protected const REPORT_ID = '902072d8-a2cc-11ed-a8fc-0242ac120002';

    /**
     * @return void
     */
    public function testBuildFromStepResponseDtoShouldReturnReportDto(): void
    {
        // Arrange
        $evaluatorReportDto = $this->createEvaluatorReportDto();
        $reportDtoBuilder = $this->createReportDtoBuilder();

        // Act
        $reportDto = $reportDtoBuilder->buildReportDto($evaluatorReportDto);

        // Assert
        $this->assertSame(ReportDtoBuilder::REPORT_NAME, $reportDto->getName());
        $this->assertSame(ReportDtoBuilder::REPORT_VERSION, $reportDto->getVersion());
        $this->assertSame(ReportDtoBuilder::REPORT_SCOPE, $reportDto->getScope());

        $reportLines = $reportDto->getPayload()->getReport()->getReportLines();
        $this->assertCount(1, $reportLines);
        $this->assertSame('docUrl', $reportLines[0]->getDocUrl());
        $this->assertSame('checker', $reportLines[0]->getCheckerName());
        $this->assertCount(1, $reportLines[0]->getViolations());
        $this->assertSame('message', $reportLines[0]->getViolations()[0]->getMessage());
        $this->assertSame('target', $reportLines[0]->getViolations()[0]->getTarget());

        $metadata = $reportDto->getMetadata();
        $this->assertSame(static::APP_ENV, $metadata->getAppEnv());
        $this->assertSame(static::SOURCE_CODE_PROVIDER, $metadata->getSourceCodeProvider());
        $this->assertSame(static::PROJECT_ID, $metadata->getProjectId());
        $this->assertSame(static::ORGANIZATION_NAME, $metadata->getOrganizationName());
        $this->assertSame(static::REPOSITORY_NAME, $metadata->getRepositoryName());
    }

    /**
     * @return \SprykerSdk\Evaluator\Report\Builder\ReportDtoBuilder
     */
    protected function createReportDtoBuilder(): ReportDtoBuilder
    {
        return new ReportDtoBuilder(
            static::SOURCE_CODE_PROVIDER,
            static::APP_ENV,
            static::PROJECT_ID,
            static::REPOSITORY_NAME,
            static::ORGANIZATION_NAME,
        );
    }

    /**
     * @return \SprykerSdk\Evaluator\Dto\ReportDto
     */
    protected function createEvaluatorReportDto(): ReportDto
    {
        return new ReportDto([
            new ReportLineDto(
                'checker',
                [
                    new ViolationDto('message', 'target'),
                ],
                'docUrl',
            ),
        ]);
    }
}
