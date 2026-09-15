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
     * @var string
     */
    public const ALLOWED_SEVERITY_LEVELS_KEY = 'ALLOWED_SEVERITY_LEVELS';

    private NpmInstallationValidator $npmInstallationValidator;

    private NpmAuditExecutor $npmAuditExecutor;

    private string $checkerDocUrl;

    public function __construct(NpmInstallationValidator $npmInstallationValidator, NpmAuditExecutor $npmAuditExecutor, string $checkerDocUrl = '')
    {
        $this->npmInstallationValidator = $npmInstallationValidator;
        $this->npmAuditExecutor = $npmAuditExecutor;
        $this->checkerDocUrl = $checkerDocUrl;
    }

    public function isApplicable(): bool
    {
        return $this->npmInstallationValidator->isNpmInstalled();
    }

    public function check(CheckerInputDataDto $inputData): CheckerResponseDto
    {
        try {
            $violations = $this->npmAuditExecutor->executeNpmAudit(
                $inputData->getConfiguration()[static::ALLOWED_SEVERITY_LEVELS_KEY] ?? null,
            );
        } catch (NpmExecutorException $e) {
                $violations = [new ViolationDto(sprintf('%s: %s', static::NPM_ISSUE_MESSAGE_PREFIX, $e->getMessage()))];
        }

        return new CheckerResponseDto($violations, $this->checkerDocUrl);
    }

    public function getName(): string
    {
        return static::NAME;
    }
}
