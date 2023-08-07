<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\Builder;

use GuzzleHttp\Psr7\Request;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\Request\HttpRequestInterface;

interface HttpRequestBuilderInterface
{
    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\Request\HttpRequestInterface $request
     *
     * @return \GuzzleHttp\Psr7\Request
     */
    public function createRequest(HttpRequestInterface $request): Request;
}
