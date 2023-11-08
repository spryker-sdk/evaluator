<?php

namespace SprykerSdk\Evaluator\Checker\DiscouragedPackagesChecker;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;

class DiscouragedPackagesFetcher implements DiscouragedPackagesFetcherInterface
{
    /**
     * @var string
     */
    protected const RESULT_KEY = 'result';

    /**
     * @var string
     */
    protected const NAME_KEY = 'name';

    /**
     * @var string
     */
    protected const REASON_KEY = 'reason';

    /**
     * @var string
     */
    protected const API_ENDPOINT = '/discouraged-packages-analyze.json';

    /**
     * string
     */
    protected const API_METHOD = 'POST';

    /**
     * @var \Psr\Http\Client\ClientInterface
     */
    protected ClientInterface $httpClient;

    /**
     * @var string
     */
    protected string $releaseAppUrl;

    /**
     * @param \Psr\Http\Client\ClientInterface $httpClient
     * @param string $releaseAppUrl
     */
    public function __construct(ClientInterface $httpClient, string $releaseAppUrl)
    {
        $this->httpClient = $httpClient;
        $this->releaseAppUrl = $releaseAppUrl;
    }

    /**
     * @param array<string> $packageNames
     *
     * @return array<\SprykerSdk\Evaluator\Checker\DiscouragedPackagesChecker\DiscouragedPackageDto>
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function fetchDiscouragedPackagesByPackageNames(array $packageNames): array
    {
        $response = $this->httpClient->sendRequest(
            new Request(
                static::API_METHOD,
                $this->getReleaseAppUrl() . static::API_ENDPOINT,
                ['Content-Type' =>  'application/json'],
                json_encode($packageNames, JSON_THROW_ON_ERROR),
            )
        );

        return $this->buildDiscouragedPackageDtos(
            json_decode((string)$response->getBody(), true, 512, \JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @param array<mixed> $responseData
     *
     * @return array<\SprykerSdk\Evaluator\Checker\DiscouragedPackagesChecker\DiscouragedPackageDto>
     */
    protected function buildDiscouragedPackageDtos(array $responseData): array
    {
        if (!isset($responseData[static::RESULT_KEY])) {
            throw new \InvalidArgumentException(sprintf('Unable to find "%s" key in release app response', static::RESULT_KEY));
        }

        if (!is_array($responseData[static::RESULT_KEY])) {
            throw new \InvalidArgumentException('Result data should be an array');
        }

        $result = [];

        foreach ($responseData[static::RESULT_KEY] as $packageData) {
            if (!isset($packageData[static::NAME_KEY], $packageData[static::REASON_KEY])) {
                throw new \InvalidArgumentException('Invalid package data');
            }

            $result[] = new DiscouragedPackageDto($packageData[static::NAME_KEY], $packageData[static::REASON_KEY]);
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getReleaseAppUrl(): string
    {
        return rtrim($this->releaseAppUrl);
    }
}
