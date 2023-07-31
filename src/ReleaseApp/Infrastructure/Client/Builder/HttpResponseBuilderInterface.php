<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\Builder;

use Psr\Http\Message\ResponseInterface;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Response\ResponseInterface as DomainResponse;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\Request\HttpRequestInterface;

interface HttpResponseBuilderInterface
{
    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\Request\HttpRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $guzzleResponse
     *
     * @return \SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Response\ResponseInterface
     */
    public function createHttpResponse(HttpRequestInterface $request, ResponseInterface $guzzleResponse): DomainResponse;
}
