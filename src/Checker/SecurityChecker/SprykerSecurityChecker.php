<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\SecurityChecker;

use Composer\Semver\Comparator;
use RuntimeException;
use SprykerSdk\Evaluator\Checker\AbstractChecker;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Dto\CheckerResponseDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;
use SprykerSdk\Evaluator\Reader\ComposerReaderInterface;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Service\ReleaseAppServiceInterface;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use SprykerSdk\Utils\Infrastructure\Helper\SemanticVersionHelper;

class SprykerSecurityChecker extends AbstractChecker
{
    /**
     * @var string
     */
    public const NAME = 'SPRYKER_SECURITY_CHECKER';

    protected ComposerReaderInterface $composerReader;

    protected ReleaseAppServiceInterface $releaseAppService;

    protected string $checkerDocUrl;

    public function __construct(
        ComposerReaderInterface $composerReader,
        ReleaseAppServiceInterface $releaseAppService,
        string $checkerDocUrl = ''
    ) {
        $this->composerReader = $composerReader;
        $this->releaseAppService = $releaseAppService;
        $this->checkerDocUrl = $checkerDocUrl;
    }

    public function getName(): string
    {
        return static::NAME;
    }

    public function check(CheckerInputDataDto $inputData): CheckerResponseDto
    {
        try {
            $releaseAppResponse = $this->releaseAppService->getNewReleaseGroups($this->createDataProviderRequest());
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

    protected function createDataProviderRequest(): UpgradeInstructionsRequest
    {
        $packages = $this->extractLockedPackages($this->composerReader->getComposerLockData());

        return new UpgradeInstructionsRequest($packages);
    }

    /**
     * @param array<string, mixed> $composerLockArray
     *
     * @return array<string, string>
     */
    protected function extractLockedPackages(array $composerLockArray): array
    {
        if (empty($composerLockArray['packages'])) {
            return [];
        }

        $locked = [];

        foreach ($composerLockArray['packages'] as $package) {
            $name = (string)$package['name'];
            if (!preg_match('#^spryker.*(?<!feature)/#', $name)) {
                continue;
            }

            $locked[$name] = (string)$package['version'];
        }

        return $locked;
    }
}
