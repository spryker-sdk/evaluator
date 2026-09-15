<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\DiscouragedPackagesChecker;

use Psr\Http\Client\ClientExceptionInterface;
use SprykerSdk\Evaluator\Checker\AbstractChecker;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Dto\CheckerResponseDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;
use SprykerSdk\Evaluator\Reader\ComposerReaderInterface;

class DiscouragedPackagesChecker extends AbstractChecker
{
    /**
     * @var string
     */
    public const NAME = 'DISCOURAGED_PACKAGES_CHECKER';

    protected DiscouragedPackagesFetcherInterface $discouragedPackagesFetcher;

    protected ComposerReaderInterface $composerReader;

    protected string $checkerDocUrl;

    public function __construct(
        DiscouragedPackagesFetcherInterface $discouragedPackagesFetcher,
        ComposerReaderInterface $composerReader,
        string $checkerDocUrl = ''
    ) {
        $this->discouragedPackagesFetcher = $discouragedPackagesFetcher;
        $this->composerReader = $composerReader;
        $this->checkerDocUrl = $checkerDocUrl;
    }

    public function check(CheckerInputDataDto $inputData): CheckerResponseDto
    {
        $projectInstalledPackages = array_keys($this->composerReader->getInstalledPackages());

        try {
            $discouragedPackages = $this->discouragedPackagesFetcher->fetchDiscouragedPackagesByPackageNames(
                $projectInstalledPackages,
            );
        } catch (ClientExceptionInterface | \InvalidArgumentException $clientException) {
            return new CheckerResponseDto(
                [new ViolationDto(sprintf('Release app api request issue: %s', $clientException->getMessage()))],
                $this->checkerDocUrl,
            );
        }

        $violations = [];

        foreach ($discouragedPackages as $discouragedPackage) {
            $violations[] = new ViolationDto($discouragedPackage->getReason(), $discouragedPackage->getPackageName());
        }

        return new CheckerResponseDto($violations, $this->checkerDocUrl);
    }

    public function getName(): string
    {
        return static::NAME;
    }
}
