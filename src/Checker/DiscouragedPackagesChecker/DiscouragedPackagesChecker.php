<?php

namespace SprykerSdk\Evaluator\Checker\DiscouragedPackagesChecker;

use SprykerSdk\Evaluator\Checker\AbstractChecker;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Dto\CheckerResponseDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;
use SprykerSdk\Evaluator\Reader\ComposerReaderInterface;
use Psr\Http\Client\ClientExceptionInterface;

class DiscouragedPackagesChecker extends AbstractChecker
{
    /**
     * @var string
     */
    public const NAME = 'DISCOURAGED_PACKAGES_CHECKER';

    /**
     * @var \SprykerSdk\Evaluator\Checker\DiscouragedPackagesChecker\DiscouragedPackagesFetcherInterface
     */
    protected DiscouragedPackagesFetcherInterface $discouragedPackagesFetcher;

    /**
     * @var \SprykerSdk\Evaluator\Reader\ComposerReaderInterface
     */
    protected ComposerReaderInterface $composerReader;

    /**
     * @var string
     */
    protected string $checkerDocUrl;

    /**
     * @param \SprykerSdk\Evaluator\Checker\DiscouragedPackagesChecker\DiscouragedPackagesFetcherInterface $discouragedPackagesFetcher
     * @param \SprykerSdk\Evaluator\Reader\ComposerReaderInterface $composerReader
     * @param string $checkerDocUrl
     */
    public function __construct(
        DiscouragedPackagesFetcherInterface $discouragedPackagesFetcher,
        ComposerReaderInterface $composerReader,
        string $checkerDocUrl = ''
    ) {
        $this->discouragedPackagesFetcher = $discouragedPackagesFetcher;
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
        $projectInstalledPackages = array_keys($this->composerReader->getComposerRequirePackages());

        try {
            $discouragedPackages = $this->discouragedPackagesFetcher->fetchDiscouragedPackagesByPackageNames(
                $projectInstalledPackages
            );
        } catch (ClientExceptionInterface $clientException) {
            return new CheckerResponseDto(
                [new ViolationDto(sprintf('Release app api request issue: %s', $clientException->getMessage()))], $this->checkerDocUrl
            );
        }

        $violations = [];

        foreach ($discouragedPackages as $discouragedPackage) {
            $violations[] = new ViolationDto($discouragedPackage->getReason(), $discouragedPackage->getPackageName());
        }

        return new CheckerResponseDto($violations, $this->checkerDocUrl);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
    }
}
