<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Report\Serializer\Normalizer;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Dto\ReportDto as EvaluatorReportDto;
use SprykerSdk\Evaluator\Dto\ReportLineDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;
use SprykerSdk\Evaluator\Report\Dto\ReportDto;
use SprykerSdk\Evaluator\Report\Dto\ReportMetadataDto;
use SprykerSdk\Evaluator\Report\Dto\ReportPayloadDto;
use SprykerSdk\Evaluator\Report\Serializer\Normalizer\ReportNormalizer;
use stdClass;

/**
 * @group SprykerSdkTest
 * @group Evaluator
 * @group Unit
 * @group Report
 * @group Serializer
 * @group Normalizer
 * @group ReportNormalizerTest
 */
class ReportNormalizerTest extends TestCase
{
    /**
     * @return void
     */
    public function testNormalizeShouldThrowExceptionWhenDataIsNotReportDto(): void
    {
        $this->expectException(InvalidArgumentException::class);
        // Arrange
        $reportDto = new stdClass();
        $reportJsonFormatter = new ReportNormalizer();

        // Act
        $reportJsonFormatter->normalize($reportDto);
    }

    /**
     * @return void
     */
    public function testNormalizeShouldReturnJsonArray(): void
    {
        // Arrange
        $reportDto = $this->createReportDto();
        $reportJsonFormatter = new ReportNormalizer();

        // Act
        $jsonArray = $reportJsonFormatter->normalize($reportDto);

        // Assert
        $this->assertSame([
            'name' => 'static_evaluator_report',
            'version' => 1,
            'scope' => 'Static Evaluator',
            'createdAt' => 1675326928,
            'payload' =>
                [
                    'report' => [
                        'checker' => [
                            'docUrl' => 'docUrl',
                            'violations' => [
                                [
                                    'message' => 'message',
                                    'target' => 'target',
                                ],
                            ],
                        ],
                    ],
                ],
            'metadata' => [
                'organization_name' => 'spryker',
                'repository_name' => 'suite',
                'project_id' => '',
                'source_code_provider' => 'github',
                'application_env' => 'CI',
                'report_id' => '902072d8-a2cc-11ed-a8fc-0242ac120002',
            ],
        ], $jsonArray);
    }

    /**
     * @return \SprykerSdk\Evaluator\Report\Dto\ReportDto
     */
    protected function createReportDto(): ReportDto
    {
        return new ReportDto(
            'static_evaluator_report',
            1,
            'Static Evaluator',
            (new DateTimeImmutable())->setTimestamp(1675326928),
            new ReportPayloadDto(
                new EvaluatorReportDto([
                    new ReportLineDto(
                        'checker',
                        [
                            new ViolationDto('message', 'target'),
                        ],
                        'docUrl',
                    ),
                ]),
            ),
            new ReportMetadataDto(
                'spryker',
                'suite',
                '',
                'github',
                'CI',
                '902072d8-a2cc-11ed-a8fc-0242ac120002',
            ),
        );
    }
}
