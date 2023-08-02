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

class SecurityUpdateChecker implements CheckerInterface
{
    /**
     * @var string
     */
    public const NAME = 'SECURITY_UPDATE_CHECKER';

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
        $violationMessages = [];

        try {
            $releaseAppResponse = $this->releaseAppService->getNewSecurityReleaseGroups($this->createDataProviderRequest());
        } catch (RuntimeException $exception) {
            $violationMessages[] = new ViolationDto(
                sprintf(
                    'Service is not available, please try latter. Error: %s %s',
                    $exception->getCode(),
                    $exception->getMessage(),
                ),
                $this->getName(),
            );

            return new CheckerResponseDto($violationMessages, $this->checkerDocUrl);
        }

        foreach ($releaseAppResponse->getReleaseGroupCollection()->toArray() as $releaseGroupDto) {
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

                $violationMessages[] = new ViolationDto(
                    sprintf(
                        'Security update available for the module %s, actual version %s',
                        $moduleDto->getName(),
                        $installedVersion,
                    ),
                    sprintf('%s:%s', $moduleDto->getName(), $moduleDto->getVersion()),
                );
            }
        }

        return new CheckerResponseDto($violationMessages, $this->checkerDocUrl);
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
