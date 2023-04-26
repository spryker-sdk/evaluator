<?php

declare(strict_types=1);

namespace ValidProject\src\Pyz\Zed\Console;

use SecurityChecker\Command\SecurityCheckerCommand;
use Spryker\Zed\Development\Communication\Console\CodeTestConsole;
use Spryker\Zed\Kernel\Container;

class ConsoleDependencyProvider
{
    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return array<\Symfony\Component\Console\Command\Command>
     */
    protected function getConsoleCommands(Container $container): array
    {
        $commands = [];

        if ($this->getConfig()->isPyzDevelopmentConsoleCommandsEnabled()) {
            $commands[] = new CodeTestConsole();
        }

        if (class_exists(SecurityCheckerCommand::class)) {
            $commands[] = new SecurityCheckerCommand();
        }

        return $commands;
    }
}
