<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Reader;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Reader\ComposerReader;
use SprykerSdk\Evaluator\Resolver\PathResolverInterface;
use SprykerSdk\Utils\Infrastructure\Service\Filesystem;

class ComposerReaderTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetInstalledPackagesShouldReturnInstalledPackages(): void
    {
        // Arrange
        $lockData =
        <<<DATA
        {
            "_readme": [
                "This file locks the dependencies of your project to a known state",
                "Read more about it at https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies",
                "This file is @generated automatically"
            ],
            "content-hash": "4b225b22405e27a7b8dbf374c4e01a41",
            "packages": [
                {
                    "name": "composer/semver",
                    "version": "3.4.0",
                    "source": {
                        "type": "git",
                        "url": "https://github.com/composer/semver.git",
                        "reference": "35e8d0af4486141bc745f23a29cc2091eb624a32"
                    }
                }
            ],
            "packages-dev": [
                {
                    "name": "dealerdirect/phpcodesniffer-composer-installer",
                    "version": "v1.0.0",
                    "source": {
                        "type": "git",
                        "url": "https://github.com/PHPCSStandards/composer-installer.git",
                        "reference": "4be43904336affa5c2f70744a348312336afd0da"
                    }
                }
            ]
        }
        DATA;

        $composerReader = new ComposerReader(
            $this->createPathResolverMock(),
            $this->createFilesystemMock($lockData),
        );

        // Act
        $packages = $composerReader->getInstalledPackages();

        // Assert
        $this->assertSame(['composer/semver', 'dealerdirect/phpcodesniffer-composer-installer'], array_keys($packages));
        $this->assertSame($packages['composer/semver']['version'], '3.4.0');
    }

    /**
     * @param string $returnData
     *
     * @return \SprykerSdk\Utils\Infrastructure\Service\Filesystem
     */
    protected function createFilesystemMock(string $returnData): Filesystem
    {
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('readFile')->willReturn($returnData);

        return $filesystem;
    }

    /**
     * @return \SprykerSdk\Evaluator\Resolver\PathResolverInterface
     */
    protected function createPathResolverMock(): PathResolverInterface
    {
        $pathResolver = $this->createMock(PathResolverInterface::class);
        $pathResolver->method('resolvePath')->willReturn('');

        return $pathResolver;
    }
}
