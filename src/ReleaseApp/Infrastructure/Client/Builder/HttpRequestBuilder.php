<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\Builder;

use GuzzleHttp\Psr7\Request;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\Request\HttpRequestInterface;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Configuration\ConfigurationProvider;

class HttpRequestBuilder implements HttpRequestBuilderInterface
{
    /**
     * @var array<string, string>
     */
    protected const HTTP_HEADER_LIST = ['Content-Type' => 'application/json'];

    /**
     * @var \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $releaseAppConfig;

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     */
    public function __construct(ConfigurationProvider $configurationProvider)
    {
        $this->releaseAppConfig = $configurationProvider;
    }

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\Request\HttpRequestInterface $request
     *
     * @return \GuzzleHttp\Psr7\Request
     */
    public function createRequest(HttpRequestInterface $request): Request
    {
        return new Request(
            $request->getMethod(),
            $this->getBaseUrl() . $request->getEndpoint(),
            static::HTTP_HEADER_LIST,
            $request->getRequest()->getBody(),
        );
    }

    /**
     * @return string
     */
    protected function getBaseUrl(): string
    {
        return $this->releaseAppConfig->getReleaseAppUrl();
    }
}
