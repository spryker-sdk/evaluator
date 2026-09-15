<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client;

use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\ReleaseAppClientInterface;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Response\ResponseInterface;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeInstructions;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\Builder\HttpRequestBuilderInterface;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\Builder\HttpResponseBuilderInterface;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\Request\HttpRequestInterface;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\Request\HttpUpgradeInstructionsRequest;

class HttpReleaseAppClient implements ReleaseAppClientInterface
{
    protected HttpRequestBuilderInterface $requestBuilder;

    protected HttpResponseBuilderInterface $responseBuilder;

    protected HttpRequestExecutorInterface $requestExecutor;

    public function __construct(
        HttpRequestBuilderInterface $requestBuilder,
        HttpResponseBuilderInterface $responseBuilder,
        HttpRequestExecutorInterface $requestExecutor
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->responseBuilder = $responseBuilder;
        $this->requestExecutor = $requestExecutor;
    }

    public function getUpgradeInstructions(UpgradeInstructionsRequest $instructionsRequest): UpgradeInstructions
    {
        /** @var \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeInstructions $response */
        $response = $this->getResponse(new HttpUpgradeInstructionsRequest($instructionsRequest));

        return $response;
    }

    protected function getResponse(HttpRequestInterface $request): ResponseInterface
    {
        $guzzleRequest = $this->requestBuilder->createRequest($request);
        $guzzleResponse = $this->requestExecutor->execute($guzzleRequest);
        $response = $this->responseBuilder->createHttpResponse($request, $guzzleResponse);

        return $response;
    }
}
