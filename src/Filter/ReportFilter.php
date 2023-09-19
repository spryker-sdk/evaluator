<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Filter;

use InvalidArgumentException;
use SprykerSdk\Evaluator\Dto\ReportDto;
use SprykerSdk\Evaluator\Dto\ReportLineDto;
use SprykerSdk\Evaluator\Dto\ToolingSettingsDto;
use SprykerSdk\Evaluator\Dto\ToolingSettingsIgnoreErrorDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;

class ReportFilter implements ReportFilterInterface
{
    /**
     * @param \SprykerSdk\Evaluator\Dto\ReportDto $reportDto
     * @param \SprykerSdk\Evaluator\Dto\ToolingSettingsDto $toolingSettingsDto
     *
     * @return \SprykerSdk\Evaluator\Dto\ReportDto
     */
    public function filterReport(ReportDto $reportDto, ToolingSettingsDto $toolingSettingsDto): ReportDto
    {
        $regexps = $this->getRegexpsByCheckers($toolingSettingsDto->getIgnoreErrors());

        foreach ($reportDto->getReportLines() as $reportLine) {
            $this->filterReportLineViolations($reportLine, $regexps);
        }

        return $reportDto;
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\ReportLineDto $reportLine
     * @param array<mixed> $regexps
     *
     * @return void
     */
    protected function filterReportLineViolations(ReportLineDto $reportLine, array $regexps): void
    {
        $violations = [];

        foreach ($reportLine->getViolations() as $violation) {
            if ($this->isViolationShouldBeIgnored($reportLine->getCheckerName(), $violation, $regexps)) {
                continue;
            }

            $violations[] = $violation;
        }

        $reportLine->setViolations($violations);
    }

    /**
     * @param string $checkerName
     * @param \SprykerSdk\Evaluator\Dto\ViolationDto $violationDto
     * @param array<mixed> $regexps
     *
     * @return bool
     */
    protected function isViolationShouldBeIgnored(string $checkerName, ViolationDto $violationDto, array $regexps): bool
    {
        foreach ($regexps['generic'] as $regexp) {
            if ($this->checkRegexp($regexp, $violationDto->getMessage()) || $this->checkRegexp($regexp, $violationDto->getTarget())) {
                return true;
            }
        }

        if (!isset($regexps['checkers'][$checkerName])) {
            return false;
        }

        foreach ($regexps['checkers'][$checkerName] as $regexp) {
            if ($this->checkRegexp($regexp, $violationDto->getMessage()) || $this->checkRegexp($regexp, $violationDto->getTarget())) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $regexp
     * @param string $string
     *
     * @return bool
     */
    protected function checkRegexp(string $regexp, string $string): bool
    {
        // disallow using of @
        set_error_handler(static function (int $errNo, string $errMessage) use ($regexp): void {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid regexp "%s". Error: %s.',
                    $regexp,
                    $errMessage,
                ),
            );
        });

        try {
            $result = preg_match($regexp, $string);
        } finally {
            restore_error_handler();
        }

        return (bool)$result;
    }

    /**
     * @param array<\SprykerSdk\Evaluator\Dto\ToolingSettingsIgnoreErrorDto> $toolingSettingsIgnoreErrorDtos
     *
     * @return array<mixed>
     */
    protected function getRegexpsByCheckers(array $toolingSettingsIgnoreErrorDtos): array
    {
        $regexps = [
            'generic' => [],
            'checkers' => [],
        ];

        foreach ($toolingSettingsIgnoreErrorDtos as $toolingSettingsIgnoreErrorDto) {
            $regexps = $this->getRegexpsFromToolingSettingsIgnoreErrorDto($toolingSettingsIgnoreErrorDto, $regexps);
        }

        return $this->makeRegexpsUniqueFlatArray($regexps);
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\ToolingSettingsIgnoreErrorDto $toolingSettingsIgnoreErrorDto
     * @param array<mixed> $regexps
     *
     * @return array<mixed>
     */
    protected function getRegexpsFromToolingSettingsIgnoreErrorDto(ToolingSettingsIgnoreErrorDto $toolingSettingsIgnoreErrorDto, array $regexps): array
    {
        if ($toolingSettingsIgnoreErrorDto->getCheckerName() === null) {
            $regexps['generic'][] = $toolingSettingsIgnoreErrorDto->getMessageRegexps();

            return $regexps;
        }

        $checkerName = $toolingSettingsIgnoreErrorDto->getCheckerName();

        if (!isset($regexps['checkers'][$checkerName])) {
            $regexps['checkers'][$checkerName] = [];
        }

        $regexps['checkers'][$checkerName][] = $toolingSettingsIgnoreErrorDto->getMessageRegexps();

        return $regexps;
    }

    /**
     * @param array<mixed> $regexps
     *
     * @return array<mixed>
     */
    protected function makeRegexpsUniqueFlatArray(array $regexps): array
    {
        $regexps['generic'] = array_unique(array_merge(...$regexps['generic']));

        foreach ($regexps['checkers'] as $checker => $checkerRegexps) {
            $regexps['checkers'][$checker] = array_unique(array_merge(...$checkerRegexps));
        }

        return $regexps;
    }
}
