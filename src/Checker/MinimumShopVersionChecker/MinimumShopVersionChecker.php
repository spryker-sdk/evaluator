<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\MinimumShopVersionChecker;

use SprykerSdk\Evaluator\Checker\CheckerInterface;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Dto\CheckerResponseDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;
use SprykerSdk\Evaluator\Reader\ComposerReaderInterface;

class MinimumShopVersionChecker implements CheckerInterface
{
    /**
     * @var string
     */
    public const NAME = 'MINIMUM_ALLOWED_SHOP_VERSION';

    /**
     * @var string
     */
    public const MESSAGE_INVALID_PACKAGE = 'The package "%s" version %s is not supported. The minimum allowed version is %s';

    /**
     * @var string
     */
    protected const COMPOSER_REQUIRE = 'require';

    /**
     * @var string
     */
    protected const COMPOSER_REQUIRE_DEV = 'require-dev';

    /**
     * @var string
     */
    protected const FEATURE_PACKAGE_NAME_PREFIX = 'spryker-feature';

    /**
     * @var string
     */
    protected const DEV_MASTER = 'dev-master';

    /**
     * @var \SprykerSdk\Evaluator\Reader\ComposerReaderInterface
     */
    protected ComposerReaderInterface $composerReader;

    /**
     * @var \SprykerSdk\Evaluator\Checker\MinimumShopVersionChecker\MinimumAllowedPackageVersionsReader
     */
    protected MinimumAllowedPackageVersionsReader $minimumAllowedPackageVersionsReader;

    /**
     * @var string
     */
    protected string $minimumFeatureVersion;

    /**
     * @var string
     */
    protected string $checkerDocUrl;

    /**
     * @param \SprykerSdk\Evaluator\Reader\ComposerReaderInterface $composerReader
     * @param \SprykerSdk\Evaluator\Checker\MinimumShopVersionChecker\MinimumAllowedPackageVersionsReader $minimumAllowedPackageVersionsReader
     * @param string $minimumFeatureVersion
     * @param string $checkerDocUrl
     */
    public function __construct(
        ComposerReaderInterface $composerReader,
        MinimumAllowedPackageVersionsReader $minimumAllowedPackageVersionsReader,
        string $minimumFeatureVersion,
        string $checkerDocUrl = ''
    ) {
        $this->composerReader = $composerReader;
        $this->minimumAllowedPackageVersionsReader = $minimumAllowedPackageVersionsReader;
        $this->minimumFeatureVersion = $minimumFeatureVersion;
        $this->checkerDocUrl = $checkerDocUrl;
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\CheckerInputDataDto $inputData
     *
     * @return \SprykerSdk\Evaluator\Dto\CheckerResponseDto
     */
    public function check(CheckerInputDataDto $inputData): CheckerResponseDto
    {
        $composerData = $this->composerReader->getComposerData();

        $composerPackages = array_keys(
            array_merge($composerData[static::COMPOSER_REQUIRE_DEV] ?? [], $composerData[static::COMPOSER_REQUIRE]),
        );

        $installedPackages = $this->getInstalledPackages($this->composerReader->getComposerLockData());

        $minimumAllowedPackageVersions = $this->minimumAllowedPackageVersionsReader->getMinimumAllowedPackageVersions();

        $violations = [];

        foreach ($composerPackages as $packageName) {
            if (!isset($installedPackages[$packageName])) {
                continue;
            }

            $packageVersion = $installedPackages[$packageName];

            $violation = $this->isFeaturePackage($packageName)
                ? $this->checkFeaturePackage($packageName, $packageVersion)
                : $this->checkPackage($packageName, $packageVersion, $minimumAllowedPackageVersions);

            if ($violation === null) {
                continue;
            }

            $violations[] = $violation;
        }

        return new CheckerResponseDto($violations, $this->checkerDocUrl);
    }

    /**
     * @param array<mixed> $composerData
     *
     * @return array<mixed>
     */
    protected function getInstalledPackages(array $composerData): array
    {
        $installedPackages = [];

        foreach ($composerData['packages'] as $package) {
            $installedPackages[$package['name']] = $package['version'];
        }

        return $installedPackages;
    }

    /**
     * @param string $packageName
     *
     * @return bool
     */
    protected function isFeaturePackage(string $packageName): bool
    {
        return strpos($packageName, static::FEATURE_PACKAGE_NAME_PREFIX) === 0;
    }

    /**
     * @param string $packageName
     * @param string $packageVersion
     *
     * @return \SprykerSdk\Evaluator\Dto\ViolationDto|null
     */
    protected function checkFeaturePackage(string $packageName, string $packageVersion): ?ViolationDto
    {
        if ($packageVersion === static::DEV_MASTER || version_compare($packageVersion, $this->minimumFeatureVersion, '>=')) {
            return null;
        }

        return $this->createViolation($packageName, $packageVersion, $this->minimumFeatureVersion);
    }

    /**
     * @param string $packageName
     * @param string $packageVersion
     * @param array<string, string> $minimumAllowedPackageVersions
     *
     * @return \SprykerSdk\Evaluator\Dto\ViolationDto|null
     */
    protected function checkPackage(string $packageName, string $packageVersion, array $minimumAllowedPackageVersions): ?ViolationDto
    {
        if (
            $packageVersion === static::DEV_MASTER ||
            !isset($minimumAllowedPackageVersions[$packageName]) ||
            version_compare($packageVersion, $minimumAllowedPackageVersions[$packageName], '>=')
        ) {
            return null;
        }

        return $this->createViolation($packageName, $packageVersion, $minimumAllowedPackageVersions[$packageName]);
    }

    /**
     * @param string $packageName
     * @param string $packageVersion
     * @param string $minimumAllowedVersion
     *
     * @return \SprykerSdk\Evaluator\Dto\ViolationDto
     */
    protected function createViolation(string $packageName, string $packageVersion, string $minimumAllowedVersion): ViolationDto
    {
        return new ViolationDto(
            sprintf(static::MESSAGE_INVALID_PACKAGE, $packageName, $packageVersion, $minimumAllowedVersion),
            sprintf('%s:%s', $packageName, $packageVersion),
        );
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
    }
}
