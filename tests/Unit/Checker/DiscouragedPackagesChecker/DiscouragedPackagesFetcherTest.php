<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Checker\DiscouragedPackagesChecker;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use SprykerSdk\Evaluator\Checker\DiscouragedPackagesChecker\DiscouragedPackagesFetcher;
use SprykerSdk\Evaluator\External\Http\HttpClientFactoryInterface;

class DiscouragedPackagesFetcherTest extends TestCase
{
    /**
     * @dataProvider apiInvalidResponseDataProvider
     *
     * @param string $invalidResponse
     *
     * @return void
     */
    public function testFetchDiscouragedPackagesByPackageNamesShouldThrowExceptionWithInvalidResponse(string $invalidResponse): void
    {
        // Arrange & Assert
        $this->expectException(InvalidArgumentException::class);

        $response = new Response(200, [], $invalidResponse);

        $discouragedPackagesFetcher = new DiscouragedPackagesFetcher(
            $this->createHttpClientFactoryMock($this->createClientMock($response)),
            '',
        );

        // Act
        $discouragedPackagesFetcher->fetchDiscouragedPackagesByPackageNames(['some/package']);
    }

    /**
     * @return void
     */
    public function testFetchDiscouragedPackagesByPackageNamesShouldFetchPackages(): void
    {
        // Arrange & Assert
        $response = new Response(200, [], '{"result": [{"name": "aaa/bbb", "reason": "deprecated package"}]}');

        $discouragedPackagesFetcher = new DiscouragedPackagesFetcher(
            $this->createHttpClientFactoryMock($this->createClientMock($response)),
            '',
        );

        // Act
        $discouragedPackages = $discouragedPackagesFetcher->fetchDiscouragedPackagesByPackageNames(['aaa/bbb']);

        // Assert
        $this->assertCount(1, $discouragedPackages);
        $this->assertSame('aaa/bbb', $discouragedPackages[0]->getPackageName());
        $this->assertSame('deprecated package', $discouragedPackages[0]->getReason());
    }

    /**
     * @return array<array<mixed>>
     */
    public static function apiInvalidResponseDataProvider(): array
    {
        return [
            ['{"one":"two"}'],
            ['{"result": "invalid"}'],
            ['{"result": [{"name": "aaa/bbb"}]}'],
            ['{"result": [{"reason": "reason"}]}'],
        ];
    }

    /**
     * @param \GuzzleHttp\ClientInterface $client
     *
     * @return \SprykerSdk\Evaluator\External\Http\HttpClientFactoryInterface
     */
    protected function createHttpClientFactoryMock(ClientInterface $client): HttpClientFactoryInterface
    {
        $httpClientFactory = $this->createMock(HttpClientFactoryInterface::class);
        $httpClientFactory->method('createClient')->willReturn($client);

        return $httpClientFactory;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \GuzzleHttp\ClientInterface
     */
    protected function createClientMock(ResponseInterface $response): ClientInterface
    {
        $client = $this->createMock(ClientInterface::class);
        $client->method('send')->willReturn($response);

        return $client;
    }
}
