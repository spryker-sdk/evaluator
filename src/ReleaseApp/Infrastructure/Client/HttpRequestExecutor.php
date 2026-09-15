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
    protected GuzzleHttp $guzzleClient;

    protected ConfigurationProvider $config;

    public function __construct(ConfigurationProvider $config, ?GuzzleHttp $guzzleClient = null)
    {
        $this->guzzleClient = $guzzleClient ?? new GuzzleHttp();
        $this->config = $config;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
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
