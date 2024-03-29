<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Checker\SprykerSecurityChecker;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Checker\SecurityChecker\SprykerSecurityChecker;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Dto\CheckerResponseDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;
use SprykerSdk\Evaluator\Reader\ComposerReader;
use SprykerSdk\Evaluator\Reader\ComposerReaderInterface;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest;
use SprykerSdk\Evaluator\ReleaseApp\Domain\Exception\ReleaseAppException;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Service\ReleaseAppService;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Service\ReleaseAppServiceInterface;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse;
use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use SprykerSdk\Evaluator\Resolver\PathResolverInterface;
use SprykerSdk\Utils\Infrastructure\Service\Filesystem;

/**
 * @group SprykerSdkTest
 * @group Evaluator
 * @group Unit
 * @group Checker
 * @group SecurityChecker
 * @group SecurityCheckerTest
 */
class SprykerSecurityCheckerTest extends TestCase
{
    /**
     * @var string
     */
    protected const INVALID_PROJECT_PATH = ROOT_TESTS . '/Unit/_data/InvalidProject';

    /**
     * @var string
     */
    protected const VALID_PROJECT_PATH = ROOT_TESTS . '/Unit/_data/ValidProject';

    /**
     * @return void
     */
    public function testCheckWrongLock(): void
    {
        //Arrange
        /** @var \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Service\ReleaseAppServiceInterface&\PHPUnit\Framework\MockObject\MockObject $releaseAppServiceMock */
        $releaseAppServiceMock = $this->createReleaseAppServiceMock();
        $releaseAppServiceMock->method('getNewReleaseGroups')
            ->with(new UpgradeInstructionsRequest([]));
        $composerReaderMock = $this->createMock(ComposerReaderInterface::class);
        $composerReaderMock->method('getComposerLockData')
            ->willReturn([]);
        $checker = new SprykerSecurityChecker(
            $composerReaderMock,
            $releaseAppServiceMock,
            'doc url',
        );

        //Act
        $response = $checker->check(new CheckerInputDataDto(''));

        //Assert
        $this->assertEquals(
            new CheckerResponseDto(
                [],
                'doc url',
            ),
            $response,
        );
    }

    /**
     * @return void
     */
    public function testCheckFoundViolation(): void
    {
        //Arrange
        /** @var \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Service\ReleaseAppServiceInterface&\PHPUnit\Framework\MockObject\MockObject $releaseAppServiceMock */
        $releaseAppServiceMock = $this->createReleaseAppServiceMock();
        $releaseAppServiceMock->method('getNewReleaseGroups')
            ->willReturn(
                new ReleaseAppResponse(
                    new ReleaseGroupDtoCollection(
                        [
                            new ReleaseGroupDto(
                                'RG1',
                                new ModuleDtoCollection([
                                    new ModuleDto('spryker/availability-gui', '6.6.0', 'minor'),
                                    new ModuleDto('spryker/store-gui', '4.2.1', 'path'),
                                    new ModuleDto('cart-gui', '1.2.1', 'path'),
                                ]),
                                false,
                                'RG1 link',
                                false,
                                true,
                            ),
                        ],
                    ),
                ),
            );
        $checker = new SprykerSecurityChecker(
            new ComposerReader($this->createPathResolverMock(static::INVALID_PROJECT_PATH), new Filesystem()),
            $releaseAppServiceMock,
            'doc url',
        );

        //Act
        $response = $checker->check(new CheckerInputDataDto(''));

        //Assert
        $this->assertEquals(
            new CheckerResponseDto(
                [
                    new ViolationDto(
                        'Security update available for the module spryker/availability-gui, actual version 6.5.3',
                        'spryker/availability-gui:6.6.0',
                    ),
                ],
                'doc url',
            ),
            $response,
        );
    }

    /**
     * @return void
     */
    public function testCheckNoViolation(): void
    {
        //Arrange
        /** @var \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Service\ReleaseAppServiceInterface&\PHPUnit\Framework\MockObject\MockObject $releaseAppServiceMock */
        $releaseAppServiceMock = $this->createReleaseAppServiceMock();
        $releaseAppServiceMock->method('getNewReleaseGroups')
            ->willReturn(
                new ReleaseAppResponse(
                    new ReleaseGroupDtoCollection(
                        [
                            new ReleaseGroupDto(
                                'RG1',
                                new ModuleDtoCollection([
                                    new ModuleDto('spryker/availability-gui', '6.6.0', 'minor'),
                                    new ModuleDto('spryker/store-gui', '4.2.1', 'path'),
                                    new ModuleDto('cart-gui', '1.2.1', 'path'),
                                ]),
                                false,
                                'RG1 link',
                                false,
                                true,
                            ),
                        ],
                    ),
                ),
            )
            ->with(new UpgradeInstructionsRequest([
                'spryker/availability-gui' => '6.6.0',
                'spryker/store-gui' => '3.2.0',
                'spryker/cart-gui' => '2.2.0',
            ]));
        $checker = new SprykerSecurityChecker(
            new ComposerReader($this->createPathResolverMock(static::VALID_PROJECT_PATH), new Filesystem()),
            $releaseAppServiceMock,
            'doc url',
        );

        //Act
        $response = $checker->check(new CheckerInputDataDto(''));

        //Assert
        $this->assertEquals(new CheckerResponseDto([], 'doc url'), $response);
    }

    /**
     * @return void
     */
    public function testCheckServiceUnavailable(): void
    {
        //Arrange
        $checker = new SprykerSecurityChecker(
            new ComposerReader($this->createPathResolverMock(static::INVALID_PROJECT_PATH), new Filesystem()),
            $this->createReleaseAppServiceMockThrowException(),
            'doc url',
        );

        //Act
        $response = $checker->check(new CheckerInputDataDto('tests/Unit/Acceptance/InvalidProject'));

        //Assert
        $this->assertEquals(
            new CheckerResponseDto(
                [
                    new ViolationDto(
                        'Service is not available, please try latter. Error: 0 Something went wrong',
                        'SPRYKER_SECURITY_CHECKER',
                    ),
                ],
                'doc url',
            ),
            $response,
        );
    }

    /**
     * @return \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Service\ReleaseAppServiceInterface
     */
    protected function createReleaseAppServiceMock(): ReleaseAppServiceInterface
    {
        return $this->createMock(ReleaseAppServiceInterface::class);
    }

    /**
     * @return \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Service\ReleaseAppServiceInterface
     */
    protected function createReleaseAppServiceMockThrowException(): ReleaseAppServiceInterface
    {
        $executorMock = $this->createMock(ReleaseAppService::class);
        $executorMock->expects($this->any())
            ->method('getNewReleaseGroups')
            ->willThrowException(
                new ReleaseAppException('Something went wrong'),
            );

        return $executorMock;
    }

    /**
     * @param string $path
     *
     * @return \SprykerSdk\Evaluator\Resolver\PathResolverInterface
     */
    protected function createPathResolverMock(string $path): PathResolverInterface
    {
        $pathResolver = $this->createMock(PathResolverInterface::class);
        $pathResolver->method('resolvePath')->willReturn($path);

        return $pathResolver;
    }
}
