<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy;

use SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategyResponse;
use SprykerSdk\Evaluator\Dto\ViolationDto;

class SdkPhpVersionStrategy implements PhpVersionCheckerStrategyInterface
{
    /**
     * @var string
     */
    public const MESSAGE_INVALID_PHP_VERSION = 'SDK use not allowed PHP version';

    /**
     * @var array<string>
     */
    protected array $sdkPhpVersions;

    /**
     * @param array<string> $sdkPhpVersions
     */
    public function __construct(array $sdkPhpVersions)
    {
        $this->sdkPhpVersions = $sdkPhpVersions;
    }

    /**
     * @param array<string> $allowedPhpVersions
     * @param string $path
     *
     * @return \SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategyResponse
     */
    public function check(array $allowedPhpVersions, string $path): CheckerStrategyResponse
    {
        $usedPhpVersions = array_intersect($allowedPhpVersions, $this->sdkPhpVersions);

        if (count($usedPhpVersions) === 0) {
            return new CheckerStrategyResponse([], [new ViolationDto(static::MESSAGE_INVALID_PHP_VERSION, $this->getTarget($path))]);
        }

        return new CheckerStrategyResponse($this->sdkPhpVersions, []);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function getTarget(string $path): string
    {
        return 'SDK php versions';
    }
}
