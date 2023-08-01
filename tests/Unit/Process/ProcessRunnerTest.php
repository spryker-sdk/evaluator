<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Process;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Process\ProcessRunner;

class ProcessRunnerTest extends TestCase
{
    /**
     * @return void
     */
    public function testRunShouldReturnProcess(): void
    {
        //Arrange
        $processRunner = new ProcessRunner();

        //Act
        $process = $processRunner->run(['ls']);

        //Assert
        $this->assertTrue($process->isSuccessful());
    }
}
