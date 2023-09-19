<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Reader;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Filesystem\Filesystem;
use SprykerSdk\Evaluator\Reader\ToolingSettingsFileReader;
use SprykerSdk\Evaluator\Resolver\PathResolverInterface;

class ToolingSettingsFileReaderTest extends TestCase
{
    /**
     * @return void
     */
    public function testReadFromFileShouldReturnEmptyArrayWhenFileDoesNotExists(): void
    {
        // Arrange
        /** @var \SprykerSdk\Evaluator\Filesystem\Filesystem&\PHPUnit\Framework\MockObject\MockObject $fileSystemMock */
        $fileSystemMock = $this->createMock(Filesystem::class);
        $fileSystemMock
            ->expects($this->once())
            ->method('exists')
            ->with('/project/tooling.yml')
            ->willReturn(false);

        $fileReader = new ToolingSettingsFileReader(
            $this->createPathResolverMock(),
            'tooling.yml',
            $fileSystemMock,
        );

        // Act
        $result = $fileReader->readFromFile();

        // Assert
        $this->assertEmpty($result);
    }

    /**
     * @return void
     */
    public function testReadFromFileShouldReturnEmptyArrayWhenFileEmpty(): void
    {
        // Arrange
        $fileReader = new ToolingSettingsFileReader(
            $this->createPathResolverMock(),
            'tooling.yml',
            $this->createFilesystemMock(''),
        );

        // Act
        $result = $fileReader->readFromFile();

        // Assert
        $this->assertEmpty($result);
    }

    /**
     * @return \SprykerSdk\Evaluator\Resolver\PathResolverInterface
     */
    protected function createPathResolverMock(): PathResolverInterface
    {
        $pathResolver = $this->createMock(PathResolverInterface::class);
        $pathResolver->method('getProjectDir')->willReturn('/project/');

        return $pathResolver;
    }

    /**
     * @param string $fileContent
     *
     * @return \SprykerSdk\Evaluator\Filesystem\Filesystem
     */
    protected function createFilesystemMock(string $fileContent): Filesystem
    {
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('exists')->willReturn(true);
        $filesystem->method('readFile')->willReturn($fileContent);

        return $filesystem;
    }
}
