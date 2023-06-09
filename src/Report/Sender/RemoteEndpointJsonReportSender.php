<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Report\Sender;

use GuzzleHttp\ClientInterface;
use SprykerSdk\Evaluator\Report\Dto\ReportDto;
use Symfony\Component\Serializer\SerializerInterface;

class RemoteEndpointJsonReportSender implements ReportSenderInterface
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected ClientInterface $httpClient;

    /**
     * @var \Symfony\Component\Serializer\SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * @var string
     */
    protected string $reportSendAuthToken;

    /**
     * @var string
     */
    protected string $endpointUrl;

    /**
     * @var int
     */
    protected int $timeout;

    /**
     * @var int
     */
    protected int $connectionTimeout;

    /**
     * @param \GuzzleHttp\ClientInterface $httpClient
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer
     * @param string $reportSendAuthToken
     * @param string $endpointUrl
     * @param int $timeout
     * @param int $connectionTimeout
     */
    public function __construct(
        ClientInterface $httpClient,
        SerializerInterface $serializer,
        string $reportSendAuthToken,
        string $endpointUrl,
        int $timeout,
        int $connectionTimeout
    ) {
        $this->httpClient = $httpClient;
        $this->serializer = $serializer;
        $this->reportSendAuthToken = $reportSendAuthToken;
        $this->endpointUrl = $endpointUrl;
        $this->timeout = $timeout;
        $this->connectionTimeout = $connectionTimeout;
    }

    /**
     * @param \SprykerSdk\Evaluator\Report\Dto\ReportDto $reportDto
     *
     * @return void
     */
    public function send(ReportDto $reportDto): void
    {
        $this->httpClient->request(
            'POST',
            $this->endpointUrl,
            [
                'query' => ['token' => $this->reportSendAuthToken],
                'headers' => ['Content-Type' => 'application/json'],
                'body' => $this->serializer->serialize($reportDto, 'json'),
                'timeout' => $this->timeout,
                'connect_timeout' => $this->connectionTimeout,
            ],
        );
    }
}
