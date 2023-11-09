<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\DiscouragedPackagesChecker;

use GuzzleHttp\Psr7\Request;
use InvalidArgumentException;
use SprykerSdk\Evaluator\External\Http\HttpClientFactoryInterface;

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
     *
     * @var string
     */
    protected const API_METHOD = 'POST';

    /**
     * @var \SprykerSdk\Evaluator\External\Http\HttpClientFactoryInterface
     */
    protected HttpClientFactoryInterface $httpClientFactory;

    /**
     * @var string
     */
    protected string $releaseAppUrl;

    /**
     * @param \SprykerSdk\Evaluator\External\Http\HttpClientFactoryInterface $httpClientFactory
     * @param string $releaseAppUrl
     */
    public function __construct(HttpClientFactoryInterface $httpClientFactory, string $releaseAppUrl)
    {
        $this->httpClientFactory = $httpClientFactory;
        $this->releaseAppUrl = $releaseAppUrl;
    }

    /**
     * @param array<string> $packageNames
     *
     * @return array<\SprykerSdk\Evaluator\Checker\DiscouragedPackagesChecker\DiscouragedPackageDto>
     */
    public function fetchDiscouragedPackagesByPackageNames(array $packageNames): array
    {
        $request = new Request(
            static::API_METHOD,
            $this->getReleaseAppUrl() . static::API_ENDPOINT,
            ['Content-Type' => 'application/json'],
            json_encode($packageNames, JSON_THROW_ON_ERROR),
        );

        $response = $this->httpClientFactory->createClient()->send($request);

        return $this->buildDiscouragedPackageDtos(
            json_decode((string)$response->getBody(), true, 512, \JSON_THROW_ON_ERROR),
        );
    }

    /**
     * @param array<mixed> $responseData
     *
     * @throws \InvalidArgumentException
     *
     * @return array<\SprykerSdk\Evaluator\Checker\DiscouragedPackagesChecker\DiscouragedPackageDto>
     */
    protected function buildDiscouragedPackageDtos(array $responseData): array
    {
        if (!isset($responseData[static::RESULT_KEY])) {
            throw new InvalidArgumentException(sprintf('Unable to find "%s" key in release app response', static::RESULT_KEY));
        }

        if (!is_array($responseData[static::RESULT_KEY])) {
            throw new InvalidArgumentException('Result data should be an array');
        }

        $result = [];

        foreach ($responseData[static::RESULT_KEY] as $packageData) {
            if (!isset($packageData[static::NAME_KEY], $packageData[static::REASON_KEY])) {
                throw new InvalidArgumentException('Invalid package data');
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
