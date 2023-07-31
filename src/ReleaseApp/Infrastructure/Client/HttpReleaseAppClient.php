<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client;

use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\ReleaseAppClientInterface;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Response\ResponseInterface;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeAnalysis;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeInstructions;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\Builder\HttpRequestBuilderInterface;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\Builder\HttpResponseBuilderInterface;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\Request\HttpRequestInterface;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\Request\HttpUpgradeAnalysisHttpRequest;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\Request\HttpUpgradeInstructionsRequest;

class HttpReleaseAppClient implements ReleaseAppClientInterface
{
    /**
     * @var \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\Builder\HttpRequestBuilderInterface
     */
    protected HttpRequestBuilderInterface $requestBuilder;

    /**
     * @var \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\Builder\HttpResponseBuilderInterface
     */
    protected HttpResponseBuilderInterface $responseBuilder;

    /**
     * @var \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\HttpRequestExecutorInterface
     */
    protected HttpRequestExecutorInterface $requestExecutor;

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\Builder\HttpRequestBuilderInterface $requestBuilder
     * @param \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\Builder\HttpResponseBuilderInterface $responseBuilder
     * @param \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\HttpRequestExecutorInterface $requestExecutor
     */
    public function __construct(
        HttpRequestBuilderInterface $requestBuilder,
        HttpResponseBuilderInterface $responseBuilder,
        HttpRequestExecutorInterface $requestExecutor
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->responseBuilder = $responseBuilder;
        $this->requestExecutor = $requestExecutor;
    }

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest $instructionsRequest
     *
     * @return \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeInstructions
     */
    public function getUpgradeInstructions(UpgradeInstructionsRequest $instructionsRequest): UpgradeInstructions
    {
        /** @var \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeInstructions $response */
        $response = $this->getResponse(new HttpUpgradeInstructionsRequest($instructionsRequest));

        return $response;
    }

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest $upgradeAnalysisRequest
     *
     * @return \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeAnalysis
     */
    public function getUpgradeAnalysis(UpgradeAnalysisRequest $upgradeAnalysisRequest): UpgradeAnalysis
    {
        /** @var \SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeAnalysis $response */
        $response = $this->getResponse(new HttpUpgradeAnalysisHttpRequest($upgradeAnalysisRequest));

        return $response;
    }

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\Request\HttpRequestInterface $request
     *
     * @return \SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Response\ResponseInterface
     */
    protected function getResponse(HttpRequestInterface $request): ResponseInterface
    {
        $guzzleRequest = $this->requestBuilder->createRequest($request);
        $guzzleResponse = $this->requestExecutor->execute($guzzleRequest);
        $response = $this->responseBuilder->createHttpResponse($request, $guzzleResponse);

        return $response;
    }
}
