<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Extractor\FeaturePackagesExtractor;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Extractor\FeaturePackagesExtractor\FeaturePackageCollector;

class FeaturePackageCollectorTest extends TestCase
{
    /**
     * @return void
     */
    public function testCollectShouldSkipNonSprykerPackagesWhenCollectNew(): void
    {
        // Arrange
        $collected = [];
        $newPackages = ['php' => '>=7.4'];
        $collector = new FeaturePackageCollector();

        // Act
        $collected = $collector->collect($collected, $newPackages);

        // Assert
        $this->assertEmpty($collected);
    }

    /**
     * @return void
     */
    public function testCollectShouldSkipGreaterPackageWhenCollectNew(): void
    {
        // Arrange
        $collected = ['spryker/agent' => '1.5.0'];
        $newPackages = ['spryker/agent' => '^1.5.1'];
        $collector = new FeaturePackageCollector();

        // Act
        $collected = $collector->collect($collected, $newPackages);

        // Assert
        $this->assertSame(['spryker/agent' => '1.5.0'], $collected);
    }

    /**
     * @return void
     */
    public function testCollectShouldCollectMinimalPackageVersionWhenCollectNew(): void
    {
        // Arrange
        $collected = ['spryker/agent' => '1.5.1'];
        $newPackages = ['spryker/agent' => '^1.5.0'];
        $collector = new FeaturePackageCollector();

        // Act
        $collected = $collector->collect($collected, $newPackages);

        // Assert
        $this->assertSame(['spryker/agent' => '1.5.0'], $collected);
    }

    /**
     * @return void
     */
    public function testCollectWhenCollectNew(): void
    {
        // Arrange
        $collected = [];
        $newPackages = ['spryker/agent' => '^1.5.0'];
        $collector = new FeaturePackageCollector();

        // Act
        $collected = $collector->collect($collected, $newPackages);

        // Assert
        $this->assertSame(['spryker/agent' => '1.5.0'], $collected);
    }
}
