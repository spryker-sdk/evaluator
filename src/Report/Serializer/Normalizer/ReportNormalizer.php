<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Report\Serializer\Normalizer;

use InvalidArgumentException;
use SprykerSdk\Evaluator\Dto\ReportDto as EvaluatorReportDto;
use SprykerSdk\Evaluator\Report\Dto\ReportDto;
use SprykerSdk\Evaluator\Report\Dto\ReportMetadataDto;
use SprykerSdk\Evaluator\Report\Dto\ReportPayloadDto;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ReportNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $data
     * @param string|null $format
     *
     * @return bool
     */
    public function supportsNormalization($data, ?string $format = null): bool
    {
        return $data instanceof ReportDto;
    }

    /**
     * @param mixed $object
     * @param string|null $format
     * @param array<string, mixed> $context
     *
     * @throws \InvalidArgumentException
     *
     * @return array<string, mixed>
     */
    public function normalize($object, ?string $format = null, array $context = []): array
    {
        if (!($object instanceof ReportDto)) {
            throw new InvalidArgumentException(
                sprintf('Invalid incoming object %s only %s is supported', get_class($object), ReportDto::class),
            );
        }

        return [
            'name' => $object->getName(),
            'version' => $object->getVersion(),
            'scope' => $object->getScope(),
            'createdAt' => $object->getCreatedAt()->getTimestamp(),
            'payload' => $this->formatPayload($object->getPayload()),
            'metadata' => $this->formatMetaData($object->getMetadata()),
        ];
    }

    /**
     * @param \SprykerSdk\Evaluator\Report\Dto\ReportPayloadDto $reportPayloadDto
     *
     * @return array<string, mixed>
     */
    protected function formatPayload(ReportPayloadDto $reportPayloadDto): array
    {
        return [
            'report' => $this->getViolationData($reportPayloadDto->getReport()),
        ];
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\ReportDto $evaluatorReportDto
     *
     * @return array<string, mixed>
     */
    protected function getViolationData(EvaluatorReportDto $evaluatorReportDto): array
    {
        $violations = [];

        foreach ($evaluatorReportDto->getReportLines() as $reportLine) {
            if (!$reportLine->getViolations()) {
                continue;
            }
            $checkerReport = [];
            $checkerReport['docUrl'] = $reportLine->getDocUrl();
            foreach ($reportLine->getViolations() as $violation) {
                $checkerReport['violations'][] = ['message' => $violation->getMessage(), 'target' => $violation->getTarget()];
            }
            $violations[$reportLine->getCheckerName()] = $checkerReport;
        }

        return $violations;
    }

    /**
     * @param \SprykerSdk\Evaluator\Report\Dto\ReportMetadataDto $metadataDto
     *
     * @return array<string, mixed>
     */
    protected function formatMetaData(ReportMetadataDto $metadataDto): array
    {
        return [
            'organization_name' => $metadataDto->getOrganizationName(),
            'repository_name' => $metadataDto->getRepositoryName(),
            'project_id' => $metadataDto->getProjectId(),
            'source_code_provider' => $metadataDto->getSourceCodeProvider(),
            'application_env' => $metadataDto->getAppEnv(),
            'report_id' => $metadataDto->getReportId(),
        ];
    }
}
