<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Evaluator\Checker\OpenSourceVulnerabilitiesChecker;

use SprykerSdk\Evaluator\Checker\AbstractChecker;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Dto\CheckerResponseDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;
use SprykerSdk\Evaluator\Resolver\PathResolverInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Process\Process;

class OpenSourceVulnerabilitiesChecker extends AbstractChecker
{
    /**
     * @var string
     */
    public const NAME = 'OPEN_SOURCE_VULNERABILITIES_CHECKER';

    /**
     * @var string
     */
    protected const NOT_AVAILABLE = 'n/a';

    /**
     * @var string
     */
    protected const MIN_REQUIRED_COMPOSER = '2.7.0';

    /**
     * @var string
     */
    protected const REGEX_SEMVER = '/\b(\d+)\.(\d+)\.(\d+)\b/';

    /**
     * @var string
     */
    protected const COMPOSER_BIN = 'composer';

    /**
     * @var string
     */
    protected const ARG_VERSION = '--version';

    /**
     * @var string
     */
    protected const ARG_NO_ANSI = '--no-ansi';

    /**
     * @var string
     */
    protected const ARG_NO_INTERACTION = '--no-interaction';

    /**
     * @var string
     */
    protected const SUBCMD_AUDIT = 'audit';

    /**
     * @var string
     */
    protected const ARG_FORMAT_JSON = '--format=json';

    /**
     * @var string
     */
    protected const ARG_ABANDONED_IGNORE = '--abandoned=ignore';

    /**
     * @var int
     */
    protected const RETRY_ATTEMPTS = 7;

    /**
     * @var int
     */
    protected const RETRY_SLEEP_SECONDS = 3;

    /**
     * @var \Symfony\Component\Console\Application
     */
    protected Application $application;

    /**
     * @var \SprykerSdk\Evaluator\Resolver\PathResolverInterface
     */
    protected PathResolverInterface $pathResolver;

    /**
     * @var string
     */
    protected string $checkerDocUrl;

    /**
     * @param \Symfony\Component\Console\Application $application
     * @param \SprykerSdk\Evaluator\Resolver\PathResolverInterface $pathResolver
     * @param string $checkerDocUrl
     */
    public function __construct(Application $application, PathResolverInterface $pathResolver, string $checkerDocUrl = '')
    {
        $this->application = $application;
        $this->pathResolver = $pathResolver;
        $this->checkerDocUrl = $checkerDocUrl;
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
     * @return \SprykerSdk\Evaluator\Dto\CheckerResponseDto
     */
    public function check(CheckerInputDataDto $inputData): CheckerResponseDto
    {
        $projectDir = $this->pathResolver->getProjectDir();

        $versionViolation = $this->ensureMinimumComposer($projectDir);
        if ($versionViolation instanceof CheckerResponseDto) {
            return $versionViolation;
        }

        $rawViolations = $this->runAuditWithRetries($projectDir);

        return $this->buildResponseFromRaw($rawViolations);
    }

    /**
     * @param array<int, string> $args
     * @param string $cwd
     *
     * @return array{0: string, 1: string, 2?: bool}
     */
    protected function runComposerCommand(array $args, string $cwd): array
    {
        $process = new Process(array_merge([static::COMPOSER_BIN], $args));
        $process->setWorkingDirectory($cwd);
        $process->run();

        return [$process->getOutput(), $process->getErrorOutput(), $process->isSuccessful()];
    }

    /**
     * @param string $projectDir
     *
     * @return \SprykerSdk\Evaluator\Dto\CheckerResponseDto|null
     */
    protected function ensureMinimumComposer(string $projectDir): ?CheckerResponseDto
    {
        [$versionStdout, $versionStderr] = $this->runComposerCommand([
            static::ARG_VERSION,
            static::ARG_NO_ANSI,
            static::ARG_NO_INTERACTION,
        ], $projectDir);

        $versionOutput = trim($versionStdout !== '' ? $versionStdout : $versionStderr);
        $detectedVersion = $this->parseComposerVersion($versionOutput);

        $minRequired = static::MIN_REQUIRED_COMPOSER;
        if ($detectedVersion === null || version_compare($detectedVersion, $minRequired, '<')) {
            $msg = sprintf(
                'Composer %s or higher is required for `composer audit --abandoned=ignore`. Detected: %s. Please upgrade Composer (e.g. `composer self-update --%s`). Raw output: %s',
                $minRequired,
                $detectedVersion ?? 'unknown',
                $minRequired,
                $versionOutput,
            );

            return new CheckerResponseDto([
                new ViolationDto($msg, static::NAME),
            ], $this->checkerDocUrl);
        }

        return null;
    }

    /**
     * @param string $projectDir
     *
     * @return string
     */
    protected function runAuditWithRetries(string $projectDir): string
    {
        $args = [
            static::SUBCMD_AUDIT,
            static::ARG_FORMAT_JSON,
            static::ARG_ABANDONED_IGNORE,
            static::ARG_NO_INTERACTION,
            static::ARG_NO_ANSI,
        ];
        for ($i = 1; $i <= static::RETRY_ATTEMPTS; $i++) {
            [$stdout, $stderr] = $this->runComposerCommand($args, $projectDir);

            if ($stdout !== '') {
                return $stdout;
            }

            if ($i < static::RETRY_ATTEMPTS) {
                sleep(static::RETRY_SLEEP_SECONDS);

                continue;
            }

            return $stderr;
        }

        return '';
    }

    /**
     * @param string $rawViolations
     *
     * @return \SprykerSdk\Evaluator\Dto\CheckerResponseDto
     */
    protected function buildResponseFromRaw(string $rawViolations): CheckerResponseDto
    {
        $decoded = json_decode($rawViolations, true);

        if (!is_array($decoded)) {
            $violationDto = new ViolationDto(
                "Internal error. Original error: $rawViolations",
                static::NAME,
            );

            return new CheckerResponseDto([$violationDto], $this->checkerDocUrl);
        }

        $advisories = $decoded['advisories'] ?? [];
        if (!is_array($advisories) || $advisories === []) {
            return new CheckerResponseDto([], $this->checkerDocUrl);
        }

        $violationMessages = [];
        foreach ($advisories as $package => $packageAdvisories) {
            $advisoryList = is_array($packageAdvisories) ? $packageAdvisories : [];
            $subject = sprintf('%s (%d advisories)', $package, count($advisoryList));

            $violationMessages[] = new ViolationDto(
                $this->createSecurityAdvisoryMessage($advisoryList),
                $subject,
            );
        }

        return new CheckerResponseDto($violationMessages, $this->checkerDocUrl);
    }

    /**
     * @param string $versionOutput
     *
     * @return string|null
     */
    protected function parseComposerVersion(string $versionOutput): ?string
    {
        if (preg_match(static::REGEX_SEMVER, $versionOutput, $m) !== 1) {
            return null;
        }

        return sprintf('%s.%s.%s', $m[1], $m[2], $m[3]);
    }

    /**
     * @param array<int, array<string, mixed>> $advisories
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
