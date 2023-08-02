<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\ReleaseApp\Infrastructure\Service;

use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\HttpRequestExecutor;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Service\ReleaseAppService;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ReleaseAppServiceTest extends KernelTestCase
{
    /**
     * @var string
     */
    protected const API_RESPONSE_DIR = 'tests/Unit/_data/ReleaseApp/Api/Response';

    /**
     * @return void
     */
    public function testGetNewReleaseGroupsSuccess(): void
    {
        // Arrange
        $container = static::bootKernel()->getContainer();
        $container->set(HttpRequestExecutor::class, $this->createRequestExecutorMock());
        $request = new UpgradeAnalysisRequest('project-name', [], []);

        // Act
        $releaseGroups = $container->get(ReleaseAppService::class)->getNewSecurityReleaseGroups($request);

        // Assert
        $this->assertEquals(
            new ReleaseAppResponse(
                new ReleaseGroupDtoCollection([
                    new ReleaseGroupDto(
                        'FRW-229 Replace Swiftmailer dependency with SymfonyMailer',
                        new ModuleDtoCollection([
                            new ModuleDto('spryker/mail-extension', '1.0.0', 'major'),
                            new ModuleDto('spryker/symfony-mailer', '1.0.2', 'major'),
                        ]),
                        true,
                        'https://api.release.spryker.com/release-group/4395',
                        true,
                        true,
                    ),
                ]),
            ),
            $releaseGroups,
        );
    }

    /**
     * @return void
     */
    public function testGetNewReleaseGroupsError(): void
    {
        // Assert
        $this->expectException(ServerException::class);

        // Arrange
        $container = static::bootKernel()->getContainer();
        $container->set(HttpRequestExecutor::class, $this->createRequestExecutorMockWithError());
        $request = new UpgradeAnalysisRequest('project-name', [], []);

        // Act
        $container->get(ReleaseAppService::class)->getNewSecurityReleaseGroups($request);
    }

    /**
     * @return \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\HttpRequestExecutor
     */
    protected function createRequestExecutorMock(): HttpRequestExecutor
    {
        $callback = function (Request $request) {
            return $this->createHttpResponse($request->getUri()->getPath());
        };

        $executorMock = $this->createMock(HttpRequestExecutor::class);
        $executorMock->expects($this->any())
            ->method('execute')
            ->will($this->returnCallback($callback));

        return $executorMock;
    }

    /**
     * @return \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Client\HttpRequestExecutor
     */
    protected function createRequestExecutorMockWithError(): HttpRequestExecutor
    {
        $executorMock = $this->createMock(HttpRequestExecutor::class);
        $executorMock->expects($this->any())
            ->method('execute')
            ->willThrowException(
                new ServerException(
                    '500 Service is unavailable',
                    new Request('GET', 'https://api.release.spryker.com'),
                    new Response(),
                ),
            );

        return $executorMock;
    }

    /**
     * @param string $endpoint
     *
     * @return \GuzzleHttp\Psr7\Response
     */
    protected function createHttpResponse(string $endpoint): Response
    {
        $contents = file_get_contents(static::API_RESPONSE_DIR . $endpoint);

        return new Response(200, [], $contents);
    }
}
