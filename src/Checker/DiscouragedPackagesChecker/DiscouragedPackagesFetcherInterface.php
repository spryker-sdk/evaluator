<?php

namespace SprykerSdk\Evaluator\Checker\DiscouragedPackagesChecker;

interface DiscouragedPackagesFetcherInterface
{
    /**
     * @param array<string> $packageNames
     *
     * @return array<\SprykerSdk\Evaluator\Checker\DiscouragedPackagesChecker\DiscouragedPackageDto>
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function fetchDiscouragedPackagesByPackageNames(array $packageNames): array;
}
