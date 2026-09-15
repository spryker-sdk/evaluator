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
     * @param array<string, mixed> $context
     */
    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof ReportDto;
    }

    /**
     * @return array<string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [ReportDto::class => true];
    }

    /**
     * @param mixed $object
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
     * @return array<string, mixed>
     */
    protected function formatPayload(ReportPayloadDto $reportPayloadDto): array
    {
        return [
            'report' => $this->getViolationData($reportPayloadDto->getReport()),
        ];
    }

    /**
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
