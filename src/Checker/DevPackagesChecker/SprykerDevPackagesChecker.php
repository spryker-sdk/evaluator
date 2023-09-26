<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\DevPackagesChecker;

use SprykerSdk\Evaluator\Checker\AbstractChecker;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Dto\CheckerResponseDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;
use SprykerSdk\Evaluator\Reader\ComposerReaderInterface;

class SprykerDevPackagesChecker extends AbstractChecker
{
    /**
     * @var string
     */
    public const NAME = 'SPRYKER_DEV_PACKAGES_CHECKER';

    /**
     * @var string
     */
    protected const SPRYKER_PACKAGE_PREFIX = 'spryker';

    /**
     * @var string
     */
    protected const DEV_PACKAGE_PREFIX = 'dev-';

    /**
     * @var string
     */
    protected const VIOLATION_MESSAGE = 'Spryker package "%s:%s" has forbidden "dev-*" version constraint';

    /**
     * @var \SprykerSdk\Evaluator\Reader\ComposerReaderInterface
     */
    protected ComposerReaderInterface $composerReader;

    /**
     * @var string
     */
    protected string $checkerDocUrl;

    /**
     * @param \SprykerSdk\Evaluator\Reader\ComposerReaderInterface $composerReader
     * @param string $checkerDocUrl
     */
    public function __construct(ComposerReaderInterface $composerReader, string $checkerDocUrl = '')
    {
        $this->composerReader = $composerReader;
        $this->checkerDocUrl = $checkerDocUrl;
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\CheckerInputDataDto $inputData
     *
     * @return \SprykerSdk\Evaluator\Dto\CheckerResponseDto
     */
    public function check(CheckerInputDataDto $inputData): CheckerResponseDto
    {
        $packages = $this->composerReader->getComposerRequirePackages();

        $sprykerPackages = array_filter(
            $packages,
            static fn (string $packageName): bool => strpos($packageName, static::SPRYKER_PACKAGE_PREFIX) === 0,
            \ARRAY_FILTER_USE_KEY,
        );

        $devSprykerPackages = array_filter(
            $sprykerPackages,
            static fn (string $constraint): bool => strpos($constraint, static::DEV_PACKAGE_PREFIX) === 0
        );

        return new CheckerResponseDto($this->createViolations($devSprykerPackages), $this->checkerDocUrl);
    }

    /**
     * @param array<string, string> $devSprykerPackages
     *
     * @return array<\SprykerSdk\Evaluator\Dto\ViolationDto>
     */
    protected function createViolations(array $devSprykerPackages): array
    {
        $violations = [];

        foreach ($devSprykerPackages as $package => $constraint) {
            $violations[] = new ViolationDto(sprintf(static::VIOLATION_MESSAGE, $package, $constraint), $package);
        }

        return $violations;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
    }
}
