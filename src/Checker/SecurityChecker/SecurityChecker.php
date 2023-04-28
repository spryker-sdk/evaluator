<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\SecurityChecker;

use SprykerSdk\Evaluator\Checker\CheckerInterface;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class SecurityChecker implements CheckerInterface
{
    /**
     * @var string
     */
    public const NAME = 'SECURITY_CHECKER';

    /**
     * @var string
     */
    protected const COMMAND_NAME = 'security:check';

    /**
     * @var string
     */
    protected const NOT_AVAILABLE = 'n/a';

    /**
     * @var \Symfony\Component\Console\Application
     */
    protected Application $application;

    /**
     * @var string|null
     */
    protected ?string $projectDirEnv;

    /**
     * @param \Symfony\Component\Console\Application $application
     * @param string|null $projectDirEnv
     */
    public function __construct(Application $application, ?string $projectDirEnv)
    {
        $this->application = $application;
        $this->projectDirEnv = $projectDirEnv;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\CheckerInputDataDto $inputData
     *
     * @return array<\SprykerSdk\Evaluator\Dto\ViolationDto>
     */
    public function check(CheckerInputDataDto $inputData): array
    {
        $securityOutput = new BufferedOutput();
        $this->application->setAutoExit(false);
        $this->application->run(
            new ArrayInput([
                'command' => static::COMMAND_NAME,
                '--path' => $this->projectDirEnv,
                '--format' => 'json',
            ]),
            $securityOutput,
        );

        $violations = json_decode($securityOutput->fetch(), true);

        if (!is_array($violations)) {
            return [new ViolationDto(
                'Internal error',
                static::NAME,
            )];
        }

        $violationMessages = [];

        foreach ($violations as $package => $violation) {
            $violationMessages[] = new ViolationDto(
                $this->createSecurityAdvisoryMessage($violation['advisories']),
                sprintf('%s: %s', $package, $violation['version'] ?? static::NOT_AVAILABLE),
            );
        }

        return $violationMessages;
    }

    /**
     * @param array<int, array<string, string>> $advisories
     *
     * @return string
     */
    protected function createSecurityAdvisoryMessage(array $advisories): string
    {
        $messages = [];

        foreach ($advisories as $advisory) {
            $messages[] = sprintf(
                '%s (%s): %s',
                $advisory['title'] ?? static::NOT_AVAILABLE,
                $advisory['cve'] ?? static::NOT_AVAILABLE,
                $advisory['link'] ?? '',
            );
        }

        return implode(PHP_EOL, $messages);
    }
}
