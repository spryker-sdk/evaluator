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
    public const MESSAGE_INVALID_PHP_VERSION = 'SDK use not allowed php version';

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
     *
     * @return \SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategyResponse
     */
    public function check(array $allowedPhpVersions): CheckerStrategyResponse
    {
        $usedPhpVersions = array_intersect($allowedPhpVersions, $this->sdkPhpVersions);

        if (count($usedPhpVersions) === 0) {
            return new CheckerStrategyResponse([], [new ViolationDto(static::MESSAGE_INVALID_PHP_VERSION, $this->getTarget())]);
        }

        return new CheckerStrategyResponse($this->sdkPhpVersions, []);
    }

    /**
     * @return string
     */
    public function getTarget(): string
    {
        return 'SDK php versions';
    }
}
