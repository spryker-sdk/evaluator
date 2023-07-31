<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\NpmChecker;

use JsonException;
use SprykerSdk\Evaluator\Dto\ViolationDto;
use SprykerSdk\Evaluator\Process\ProcessRunnerInterface;

class NpmAuditExecutor
{
    /**
     * @var string
     */
    protected const VULNERABILITIES_KEY = 'vulnerabilities';

    /**
     * @var array<string>
     */
    protected const SEVERITY_LEVELS = ['low', 'moderate', 'high', 'critical'];

    /**
     * @var string
     */
    protected const SEVERITY_KEY = 'severity';

    /**
     * @var string
     */
    protected const NAME_KEY = 'name';

    /**
     * @var string
     */
    protected const VIA_KEY = 'via';

    /**
     * @var string
     */
    protected const TITLE_KEY = 'title';

    /**
     * @var string
     */
    protected const URL_KEY = 'url';

    /**
     * @var \SprykerSdk\Evaluator\Process\ProcessRunnerInterface
     */
    private ProcessRunnerInterface $processRunner;

    /**
     * @param \SprykerSdk\Evaluator\Process\ProcessRunnerInterface $processRunner
     */
    public function __construct(ProcessRunnerInterface $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * @throws \SprykerSdk\Evaluator\Checker\NpmChecker\NpmExecutorException
     *
     * @return array<\SprykerSdk\Evaluator\Dto\ViolationDto>
     */
    public function executeNpmAudit(): array
    {
        $process = $this->processRunner->run(['npm', 'audit', '--json']);

        if ($process->isSuccessful()) {
            return [];
        }

        $stdOut = trim($process->getOutput());
        $stdErr = trim($process->getErrorOutput());

        if ($stdErr && !$stdOut) {
            throw new NpmExecutorException($stdErr);
        }

        try {
            $report = json_decode($stdOut, true, 512, \JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new NpmExecutorException(sprintf('Out: %s Err: %s', $stdOut, $stdErr));
        }

        $this->assertKeyExists($report, static::VULNERABILITIES_KEY);
        $this->assertArray($report[static::VULNERABILITIES_KEY]);

        return $this->getUniqueViolations(
            $this->getViolationsFromReport($report[static::VULNERABILITIES_KEY]),
        );
    }

    /**
     * @param array<mixed> $violations
     *
     * @return array<\SprykerSdk\Evaluator\Dto\ViolationDto>
     */
    protected function getViolationsFromReport(array $violations): array
    {
        $violationDtos = [];

        foreach ($violations as $violation) {
            $this->assertViolationData($violation);

            $severity = $violation[static::SEVERITY_KEY];

            if (!in_array($severity, static::SEVERITY_LEVELS, true)) {
                continue;
            }

            foreach ($violation[static::VIA_KEY] as $violationSource) {
                if (!is_array($violationSource)) {
                    continue;
                }

                $violationDto = $this->processViolationSource($violationSource, $severity, $violation[static::NAME_KEY]);

                if ($violationDto === null) {
                    continue;
                }

                $violationDtos[] = $violationDto;
            }
        }

        return $violationDtos;
    }

    /**
     * @param array<mixed> $violationSource
     * @param string $severity
     * @param string $target
     *
     * @return \SprykerSdk\Evaluator\Dto\ViolationDto|null
     */
    protected function processViolationSource(array $violationSource, string $severity, string $target): ?ViolationDto
    {
        if (!isset($violationSource[static::TITLE_KEY])) {
            return null;
        }

        $message = $violationSource[static::TITLE_KEY];

        if (isset($violationSource[static::URL_KEY])) {
            $message .= PHP_EOL . $violationSource[static::URL_KEY];
        }

        return new ViolationDto(sprintf('[%s] %s', $severity, $message), $target);
    }

    /**
     * @param array<\SprykerSdk\Evaluator\Dto\ViolationDto> $violations
     *
     * @return array<\SprykerSdk\Evaluator\Dto\ViolationDto>
     */
    protected function getUniqueViolations(array $violations): array
    {
        $uniqueViolations = [];
        $processedViolations = [];

        foreach ($violations as $violation) {
            $violationHash = sha1($violation->getTarget() . $violation->getMessage());

            if (in_array($violationHash, $processedViolations, true)) {
                continue;
            }

            $uniqueViolations[] = $violation;
            $processedViolations[] = $violationHash;
        }

        return $uniqueViolations;
    }

    /**
     * @param array<mixed> $violation
     *
     * @return void
     */
    protected function assertViolationData(array $violation): void
    {
        $this->assertKeyExists($violation, static::SEVERITY_KEY);
        $this->assertKeyExists($violation, static::NAME_KEY);
        $this->assertKeyExists($violation, static::VIA_KEY);
        $this->assertArray($violation[static::VIA_KEY]);
    }

    /**
     * @param array<mixed> $report
     * @param string $key
     *
     * @throws \SprykerSdk\Evaluator\Checker\NpmChecker\NpmExecutorException
     *
     * @return void
     */
    protected function assertKeyExists(array $report, string $key): void
    {
        if (isset($report[$key])) {
            return;
        }

        throw new NpmExecutorException(
            sprintf('Unable to find "%s" key in output array "%s"', $key, substr(var_export($report, true), 0, 500)),
        );
    }

    /**
     * @param mixed $value
     *
     * @throws \SprykerSdk\Evaluator\Checker\NpmChecker\NpmExecutorException
     *
     * @return void
     */
    protected function assertArray($value): void
    {
        if (is_array($value)) {
            return;
        }

        throw new NpmExecutorException(
            sprintf('Value should be an array %s', substr(var_export($value, true), 0, 500)),
        );
    }
}
