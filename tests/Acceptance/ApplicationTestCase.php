<?php

namespace SprykerSdkTest\Evaluator\Acceptance;

use SprykerSdk\Evaluator\Console\Command\EvaluatorCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ApplicationKernelTestCase extends KernelTestCase
{
    /**
     * @param string $projectPath
     *
     * @return \Symfony\Component\Console\Tester\CommandTester
     */
    protected function createCommandTester(string $projectPath): CommandTester
    {
        $_ENV['EVALUATOR_PROJECT_DIR'] = $projectPath;
        $application = new Application(static::bootKernel());
        $command = $application->find(EvaluatorCommand::COMMAND_NAME);

        return new CommandTester($command);
    }
}
