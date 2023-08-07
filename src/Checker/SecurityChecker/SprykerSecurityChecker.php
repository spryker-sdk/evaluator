<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\SecurityChecker;

use Composer\Semver\Comparator;
use RuntimeException;
use SprykerSdk\Evaluator\Checker\CheckerInterface;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Dto\CheckerResponseDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;
use SprykerSdk\Evaluator\Helper\SemanticVersionHelper;
use SprykerSdk\Evaluator\Reader\ComposerReaderInterface;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Service\ReleaseAppServiceInterface;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;

class SprykerSecurityChecker implements CheckerInterface
{
    /**
     * @var string
     */
    public const NAME = 'SPRYKER_SECURITY_CHECKER';

    /**
     * @var \SprykerSdk\Evaluator\Reader\ComposerReaderInterface
     */
    protected ComposerReaderInterface $composerReader;

    /**
     * @var \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Service\ReleaseAppServiceInterface
     */
    protected ReleaseAppServiceInterface $releaseAppService;

    /**
     * @var string
     */
    protected string $checkerDocUrl;

    /**
     * @param \SprykerSdk\Evaluator\Reader\ComposerReaderInterface $composerReader
     * @param \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Service\ReleaseAppServiceInterface $releaseAppService
     * @param string $checkerDocUrl
     */
    public function __construct(
        ComposerReaderInterface $composerReader,
        ReleaseAppServiceInterface $releaseAppService,
        string $checkerDocUrl = ''
    ) {
        $this->composerReader = $composerReader;
        $this->releaseAppService = $releaseAppService;
        $this->checkerDocUrl = $checkerDocUrl;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\CheckerInputDataDto $inputData
     *
     * @return \SprykerSdk\Evaluator\Dto\CheckerResponseDto
     */
    public function check(CheckerInputDataDto $inputData): CheckerResponseDto
    {
        try {
            $releaseAppResponse = $this->releaseAppService->getNewSecurityReleaseGroups($this->createDataProviderRequest());
        } catch (RuntimeException $exception) {
            $violation = new ViolationDto(
                sprintf(
                    'Service is not available, please try latter. Error: %s %s',
                    $exception->getCode(),
                    $exception->getMessage(),
                ),
                $this->getName(),
            );

            return new CheckerResponseDto([$violation], $this->checkerDocUrl);
        }

        return new CheckerResponseDto($this->buildViolations($releaseAppResponse), $this->checkerDocUrl);
    }

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse $releaseAppResponse
     *
     * @return array<\SprykerSdk\Evaluator\Dto\ViolationDto>
     */
    protected function buildViolations(ReleaseAppResponse $releaseAppResponse): array
    {
        $violations = [];

        foreach ($releaseAppResponse->getReleaseGroupCollection()->toArray() as $releaseGroupDto) {
            $violations = [...$violations, ...$this->buildViolationsByReleaseGroup($releaseGroupDto)];
        }

        return $violations;
    }

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $releaseGroupDto
     *
     * @return array<\SprykerSdk\Evaluator\Dto\ViolationDto>
     */
    protected function buildViolationsByReleaseGroup(ReleaseGroupDto $releaseGroupDto): array
    {
        $violations = [];

        foreach ($releaseGroupDto->getModuleCollection()->toArray() as $moduleDto) {
            $installedVersion = $this->composerReader->getPackageVersion($moduleDto->getName());
            if ($installedVersion === null) {
                continue;
            }

            $installedMajorVersion = SemanticVersionHelper::getMajorVersion($installedVersion);
            $securityUpdateMajorVersion = SemanticVersionHelper::getMajorVersion($moduleDto->getVersion());
            if ($securityUpdateMajorVersion !== $installedMajorVersion) {
                continue;
            }
            if (!Comparator::greaterThan($moduleDto->getVersion(), $installedVersion)) {
                continue;
            }

            $violations[] = new ViolationDto(
                sprintf(
                    'Security update available for the module %s, actual version %s',
                    $moduleDto->getName(),
                    $installedVersion,
                ),
                sprintf('%s:%s', $moduleDto->getName(), $moduleDto->getVersion()),
            );
        }

        return $violations;
    }

    /**
     * @return \SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest
     */
    protected function createDataProviderRequest(): UpgradeAnalysisRequest
    {
        $projectName = $this->composerReader->getProjectName();
        $composerJson = $this->composerReader->getComposerData();
        $composerLock = $this->composerReader->getComposerLockData();

        return new UpgradeAnalysisRequest($projectName, $composerJson, $composerLock);
    }
}
