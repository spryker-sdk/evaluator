<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Acceptance;

use SprykerSdk\Evaluator\Console\Command\EvaluatorCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ApplicationTestCase extends KernelTestCase
{
    /**
     * @param string $projectPath
     * @param array<string> $envs
     *
     * @return \Symfony\Component\Console\Tester\CommandTester
     */
    protected function createCommandTester(string $projectPath, array $envs = []): CommandTester
    {
        $_ENV['EVALUATOR_PROJECT_DIR'] = $projectPath;
        foreach ($envs as $name => $value) {
            $_ENV[$name] = $value;
        }
        $application = new Application(static::bootKernel());
        $command = $application->find(EvaluatorCommand::COMMAND_NAME);

        return new CommandTester($command);
    }
}
