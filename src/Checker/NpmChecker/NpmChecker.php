<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\NpmChecker;

use SprykerSdk\Evaluator\Checker\AbstractChecker;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Dto\CheckerResponseDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;

class NpmChecker extends AbstractChecker
{
    /**
     * @var string
     */
    public const NAME = 'NPM_CHECKER';

    /**
     * @var string
     */
    public const NPM_ISSUE_MESSAGE_PREFIX = 'Npm audit issue';

    /**
     * @var \SprykerSdk\Evaluator\Checker\NpmChecker\NpmInstallationValidator
     */
    private NpmInstallationValidator $npmInstallationValidator;

    /**
     * @var \SprykerSdk\Evaluator\Checker\NpmChecker\NpmAuditExecutor
     */
    private NpmAuditExecutor $npmAuditExecutor;

    /**
     * @var string
     */
    private string $checkerDocUrl;

    /**
     * @param \SprykerSdk\Evaluator\Checker\NpmChecker\NpmInstallationValidator $npmInstallationValidator
     * @param \SprykerSdk\Evaluator\Checker\NpmChecker\NpmAuditExecutor $npmAuditExecutor
     * @param string $checkerDocUrl
     */
    public function __construct(NpmInstallationValidator $npmInstallationValidator, NpmAuditExecutor $npmAuditExecutor, string $checkerDocUrl = '')
    {
        $this->npmInstallationValidator = $npmInstallationValidator;
        $this->npmAuditExecutor = $npmAuditExecutor;
        $this->checkerDocUrl = $checkerDocUrl;
    }

    /**
     * @return bool
     */
    public function isApplicable(): bool
    {
        return $this->npmInstallationValidator->isNpmInstalled();
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\CheckerInputDataDto $inputData
     *
     * @return \SprykerSdk\Evaluator\Dto\CheckerResponseDto
     */
    public function check(CheckerInputDataDto $inputData): CheckerResponseDto
    {
        try {
            $violations = $this->npmAuditExecutor->executeNpmAudit();
        } catch (NpmExecutorException $e) {
                $violations = [new ViolationDto(sprintf('%s: %s', static::NPM_ISSUE_MESSAGE_PREFIX, $e->getMessage()))];
        }

        return new CheckerResponseDto($violations, $this->checkerDocUrl);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
    }
}
