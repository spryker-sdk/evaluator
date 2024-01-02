<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy;

use Composer\Semver\Semver;
use InvalidArgumentException;
use SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy\FileReader\ComposerFileReader;
use SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategyResponse;
use SprykerSdk\Evaluator\Dto\ViolationDto;
use SprykerSdk\Evaluator\Resolver\PathResolverInterface;

class ComposerPhpVersionStrategy implements PhpVersionCheckerStrategyInterface
{
    /**
     * @var string
     */
    public const MESSAGE_NO_PHP_DEPENDENCY = 'Composer json does not contain PHP dependency';

    /**
     * @var string
     */
    public const MESSAGE_USED_NOT_ALLOWED_PHP_VERSION = 'Composer json PHP constraint "%s" does not match allowed php versions';

    /**
     * @var string
     */
    protected const MAX_MINOR_VERSION_SUFFIX = '.9999';

    /**
     * @var \SprykerSdk\Evaluator\Resolver\PathResolverInterface
     */
    protected PathResolverInterface $pathResolver;

    /**
     * @var \SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy\FileReader\ComposerFileReader
     */
    protected ComposerFileReader $composerFileReader;

    /**
     * @param \SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy\FileReader\ComposerFileReader $composerFileReader
     */
    public function __construct(ComposerFileReader $composerFileReader)
    {
        $this->composerFileReader = $composerFileReader;
    }

    /**
     * @param array<string> $allowedPhpVersions
     * @param string $path
     *
     * @return \SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategyResponse
     */
    public function check(array $allowedPhpVersions, string $path): CheckerStrategyResponse
    {
        $composerFile = $this->getTarget($path);

        try {
            $composerData = $this->composerFileReader->read($composerFile);
        } catch (InvalidArgumentException $e) {
            return new CheckerStrategyResponse([], [new ViolationDto($e->getMessage(), $composerFile)]);
        }

        if (!isset($composerData['require']['php'])) {
            return new CheckerStrategyResponse([], [new ViolationDto(static::MESSAGE_NO_PHP_DEPENDENCY, $composerFile)]);
        }

        $validVersions = $this->isExactPhpVersion($composerData)
            ? $this->compareByExactPhpVersion($allowedPhpVersions, $composerData)
            : $this->compareByMask($allowedPhpVersions, $composerData);

        if (count($validVersions) === 0) {
            return new CheckerStrategyResponse(
                [],
                [new ViolationDto(sprintf(static::MESSAGE_USED_NOT_ALLOWED_PHP_VERSION, $composerData['require']['php']), $composerFile)],
            );
        }

        return new CheckerStrategyResponse($validVersions, []);
    }

    /**
     * @param array<string> $allowedPhpVersions
     * @param array<mixed> $composerData
     *
     * @return array<string>
     */
    protected function compareByMask(array $allowedPhpVersions, array $composerData): array
    {
        return array_filter(
            $allowedPhpVersions,
            static fn (string $allowedVersion): bool => Semver::satisfies($allowedVersion . static::MAX_MINOR_VERSION_SUFFIX, $composerData['require']['php'])
        );
    }

    /**
     * @param array<string> $allowedPhpVersions
     * @param array<mixed> $composerData
     *
     * @return array<string>
     */
    protected function compareByExactPhpVersion(array $allowedPhpVersions, array $composerData): array
    {
        return array_filter(
            $allowedPhpVersions,
            static fn (string $allowedVersion): bool => strpos($composerData['require']['php'], $allowedVersion) === 0
        );
    }

    /**
     * @param array<mixed> $composerData
     *
     * @return bool
     */
    protected function isExactPhpVersion(array $composerData): bool
    {
        return (bool)preg_match('/^\d(\.\d+)*$/', $composerData['require']['php']);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function getTarget(string $path): string
    {
        return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'composer.json';
    }
}
