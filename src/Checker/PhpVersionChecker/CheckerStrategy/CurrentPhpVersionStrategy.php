<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy;

use SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategyResponse;
use SprykerSdk\Evaluator\Dto\ViolationDto;

class CurrentPhpVersionStrategy implements PhpVersionCheckerStrategyInterface
{
    /**
     * @var string
     */
    public const MESSAGE_INVALID_LOCAL_PHO_VERSION = 'Current PHP version "%s" is not allowed.';

    /**
     * @var string
     */
    protected string $currentPhpVersion;

    /**
     * @param string $currentPhpVersion
     */
    public function __construct(string $currentPhpVersion)
    {
        $this->currentPhpVersion = $currentPhpVersion;
    }

    /**
     * @param array<string> $allowedPhpVersions
     * @param string $path
     *
     * @return \SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategyResponse
     */
    public function check(array $allowedPhpVersions, string $path): CheckerStrategyResponse
    {
        $phpVersion = $this->currentPhpVersion;
        $validVersions = array_filter(
            $allowedPhpVersions,
            static fn (string $allowedVersion): bool => strpos($phpVersion, $allowedVersion) === 0 && version_compare($phpVersion, $allowedVersion, '>='),
        );

        $violations = count($validVersions) === 0
            ? [new ViolationDto(sprintf(static::MESSAGE_INVALID_LOCAL_PHO_VERSION, $this->currentPhpVersion), $this->getTarget($path))]
            : [];

        return new CheckerStrategyResponse($validVersions, $violations);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function getTarget(string $path): string
    {
        return sprintf('Current php version %s', $this->currentPhpVersion);
    }
}
