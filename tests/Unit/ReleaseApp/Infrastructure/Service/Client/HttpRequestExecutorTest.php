<?php

declare(strict_types=1);

namespace Unit\ReleaseApp\Infrastructure\Service\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\HttpRequestExecutor;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Configuration\ConfigurationProvider;

class HttpRequestExecutorTest extends TestCase
{
    /**
     * @return void
     */
    public function testExecuteWithSuccessfulResponse(): void
    {
        // Arrange
        $mockGuzzleResponse = $this->createMock(ResponseInterface::class);
        $mockRequest = $this->createMock(RequestInterface::class);
        $mockGuzzleClient = $this->createMock(Client::class);

        $mockGuzzleClient
            ->expects($this->once())
            ->method('send')
            ->with($mockRequest)
            ->willReturn($mockGuzzleResponse);

        $config = $this->createMock(ConfigurationProvider::class);

        $config
            ->expects($this->once())
            ->method('getHttpRetrieveAttemptsCount')
            ->willReturn(3);

        $httpRequestExecutor = new HttpRequestExecutor($config, $mockGuzzleClient);

        // Act
        $result = $httpRequestExecutor->execute($mockRequest);

        // Assert
        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    /**
     * @return void
     */
    public function testExecuteWithServerException(): void
    {
        // Arrange
        $mockRequest = $this->createMock(RequestInterface::class);
        $mockGuzzleClient = $this->createMock(Client::class);

        $serverException = $this->createMock(ServerException::class);

        $mockGuzzleClient
            ->expects($this->exactly(3))
            ->method('send')
            ->with($mockRequest)
            ->willThrowException($serverException);

        $config = $this->createMock(ConfigurationProvider::class);
        $config
            ->expects($this->exactly(3))
            ->method('getHttpRetrieveRetryDelay')
            ->willReturn(1);

        $config
            ->expects($this->exactly(3))
            ->method('getHttpRetrieveAttemptsCount')
            ->willReturn(3);

        $httpRequestExecutor = new HttpRequestExecutor($config, $mockGuzzleClient);

        // Assert
        $this->expectException(ServerException::class);

        // Act
        $httpRequestExecutor->execute($mockRequest);
    }
}
