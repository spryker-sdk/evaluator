<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\ReportRenderer;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Console\ReportRenderer\JsonReportRenderer;
use SprykerSdk\Evaluator\Console\ReportRenderer\OutputReportRenderer;
use SprykerSdk\Evaluator\Console\ReportRenderer\ReportRenderResolver;
use SprykerSdk\Evaluator\Dto\ReportDto;
use SprykerSdk\Evaluator\Dto\ReportLineDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @group SprykerSdkTest
 * @group Evaluator
 * @group Unit
 * @group ReportRenderer
 * @group ReportRenderResolverTest
 */
class ReportRenderResolverTest extends TestCase
{
    /**
     * @var \SprykerSdk\Evaluator\Console\ReportRenderer\ReportRenderResolver
     */
    protected ReportRenderResolver $strategy;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem&\PHPUnit\Framework\MockObject\MockObject
     */
    protected Filesystem $filesystem;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->filesystem = $this->createMock(Filesystem::class);
        $this->strategy = new ReportRenderResolver([
            new JsonReportRenderer($this->filesystem),
            new OutputReportRenderer($this->filesystem),
        ]);
    }

    /**
     * @return void
     */
    public function testConsoleOutput(): void
    {
        // Arrange
        $strategy = $this->strategy->resolve(OutputReportRenderer::NAME);
        $bufferedOutput = new BufferedOutput();

        $report = new ReportDto([
            new ReportLineDto(
                'testChecker',
                [
                    new ViolationDto('testMessage', 'target'),
                ],
                'docUrl',
            ),
        ]);

        // Assert
        $this->filesystem->expects($this->never())->method('dumpFile');

        // Act
        $strategy->render($report, $bufferedOutput);

        // Assert
        $this->assertSame(
            <<<OUT
                ===========
                TESTCHECKER
                ===========

                +---+-------------+--------+
                | # | Message     | Target |
                +---+-------------+--------+
                | 1 | testMessage | target |
                +---+-------------+--------+

                Read more: docUrl


                OUT,
            $bufferedOutput->fetch(),
        );
    }

    /**
     * @return void
     */
    public function testFileOutput(): void
    {
        // Arrange
        $strategy = $this->strategy->resolve(OutputReportRenderer::NAME);
        $bufferedOutput = new BufferedOutput();

        $report = new ReportDto([
            new ReportLineDto(
                'testChecker',
                [
                    new ViolationDto('testMessage', 'target'),
                ],
                'docUrl',
            ),
        ]);

        // Assert
        $this->filesystem->expects($this->once())
            ->method('dumpFile')
            ->with(
                'fileName.txt',
                <<<OUT
                ===========
                TESTCHECKER
                ===========

                +---+-------------+--------+
                | # | Message     | Target |
                +---+-------------+--------+
                | 1 | testMessage | target |
                +---+-------------+--------+

                Read more: docUrl


                OUT,
            );

        // Act
        $strategy->render($report, $bufferedOutput, 'fileName.*');
    }

    /**
     * @return void
     */
    public function testConsoleJsonOutput(): void
    {
        // Arrange
        $strategy = $this->strategy->resolve(JsonReportRenderer::NAME);
        $bufferedOutput = new BufferedOutput();

        $report = new ReportDto([
            new ReportLineDto(
                'testChecker',
                [
                    new ViolationDto('testMessage', 'target'),
                ],
                'docUrl',
            ),
        ]);

        // Assert
        $this->filesystem->expects($this->never())
            ->method('dumpFile');

        // Act
        $strategy->render($report, $bufferedOutput);

        // Assert
        $this->assertSame($bufferedOutput->fetch(), '{"testChecker":{"docUrl":"docUrl","violation":{"target":"target","message":"testMessage"}}}');
    }

    /**
     * @return void
     */
    public function testFileJsonOutput(): void
    {
        // Arrange
        $strategy = $this->strategy->resolve(JsonReportRenderer::NAME);

        $bufferedOutput = new BufferedOutput();

        $report = new ReportDto([
            new ReportLineDto(
                'testChecker',
                [
                    new ViolationDto('testMessage', 'target'),
                ],
                'docUrl',
            ),
        ]);

        // Assert
        $this->filesystem->expects($this->once())
            ->method('dumpFile')
            ->with(
                'fileName.json',
                '{"testChecker":{"docUrl":"docUrl","violation":{"target":"target","message":"testMessage"}}}',
            );

        // Act
        $strategy->render($report, $bufferedOutput, 'fileName.*');
    }
}
