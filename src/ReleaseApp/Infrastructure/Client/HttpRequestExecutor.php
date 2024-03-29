<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client;

use GuzzleHttp\Client as GuzzleHttp;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Exception\ReleaseAppException;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Configuration\ConfigurationProvider;

class HttpRequestExecutor implements HttpRequestExecutorInterface
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected GuzzleHttp $guzzleClient;

    /**
     * @var \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $config;

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Configuration\ConfigurationProvider $config
     * @param \GuzzleHttp\Client|null $guzzleClient
     */
    public function __construct(ConfigurationProvider $config, ?GuzzleHttp $guzzleClient = null)
    {
        $this->guzzleClient = $guzzleClient ?? new GuzzleHttp();
        $this->config = $config;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function execute(RequestInterface $request): ResponseInterface
    {
        $attempts = 0;
        $exception = null;
        $guzzleResponse = null;

        do {
            try {
                $guzzleResponse = $this->guzzleClient->send($request);
            } catch (ServerException $currentException) {
                $exception = $currentException;
                sleep($this->config->getHttpRetrieveRetryDelay());
            } finally {
                ++$attempts;
            }
        } while ($attempts < $this->config->getHttpRetrieveAttemptsCount() && $guzzleResponse == null);

        if ($guzzleResponse === null) {
            if ($exception) {
                throw $exception;
            }

            throw new ReleaseAppException('Failed to request url ' . $request->getUri());
        }

        return $guzzleResponse;
    }
}
