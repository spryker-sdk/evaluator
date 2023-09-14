<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Filesystem;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

class FilesystemTest extends TestCase
{
    /**
     * @return void
     */
    public function testReadFileShouldThrowIoExceptionWhenFileDoesNotExist(): void
    {
        // Arrange & Assert
        $this->expectException(IOException::class);

        $filesystem = new Filesystem();

        // Act
        $filesystem->readFile(__DIR__ . '/undefined.txt');
    }

    /**
     * @return void
     */
    public function testReadFileShouldThrowIoExceptionWhenTryToBoxInvalidFunction(): void
    {
        // Arrange && Assert
        $this->expectException(IOException::class);

        $invalidFilesystem = new class () extends Filesystem
        {
            /**
             * @return void
             */
            public function test(): void
            {
                static::box('invalid_function');
            }
        };

        // Act
        $invalidFilesystem->test();
    }
}
