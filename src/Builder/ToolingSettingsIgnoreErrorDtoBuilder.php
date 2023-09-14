<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Builder;

use InvalidArgumentException;
use SprykerSdk\Evaluator\Dto\ToolingSettingsIgnoreErrorDto;

class ToolingSettingsIgnoreErrorDtoBuilder
{
    /**
     * @var string
     */
    public const IGNORE_ERRORS_KEY = 'ignoreErrors';

    /**
     * @var string
     */
    public const MESSAGES_KEY = 'messages';

    /**
     * @var string
     */
    public const CHECKER_KEY = 'checker';

    /**
     * @var string
     */
    protected string $toolingFile;

    /**
     * @param string $toolingFile
     */
    public function __construct(string $toolingFile)
    {
        $this->toolingFile = $toolingFile;
    }

    /**
     * @param array<mixed>$toolingSettingsArray
     *
     * @throws \InvalidArgumentException
     *
     * @return array<\SprykerSdk\Evaluator\Dto\ToolingSettingsIgnoreErrorDto>
     */
    public function buildFromToolingSettingsArray(array $toolingSettingsArray): array
    {
        if (!isset($toolingSettingsArray[static::IGNORE_ERRORS_KEY])) {
            return [];
        }

        if (!is_array($toolingSettingsArray[static::IGNORE_ERRORS_KEY])) {
            throw new InvalidArgumentException(sprintf('Key [%s] should be an array in %s', static::IGNORE_ERRORS_KEY, $this->toolingFile));
        }

        $ignoreErrorDtos = [];

        foreach ($toolingSettingsArray[static::IGNORE_ERRORS_KEY] as $regexpValue) {
            $ignoreErrorDtos[] = $this->buildRegexpValue($regexpValue);
        }

        return $ignoreErrorDtos;
    }

    /**
     * @param mixed $regexpValue
     *
     * @throws \InvalidArgumentException
     *
     * @return \SprykerSdk\Evaluator\Dto\ToolingSettingsIgnoreErrorDto
     */
    protected function buildRegexpValue($regexpValue): ToolingSettingsIgnoreErrorDto
    {
        if (is_string($regexpValue)) {
            return new ToolingSettingsIgnoreErrorDto([$regexpValue]);
        }

        if (is_array($regexpValue)) {
            return $this->buildCheckerIgnoreErrors($regexpValue);
        }

        throw new InvalidArgumentException(sprintf('Key [%s] should consist of arrays or strings in %s', static::IGNORE_ERRORS_KEY, $this->toolingFile));
    }

    /**
     * @param array<mixed> $regexpValue
     *
     * @throws \InvalidArgumentException
     *
     * @return \SprykerSdk\Evaluator\Dto\ToolingSettingsIgnoreErrorDto
     */
    protected function buildCheckerIgnoreErrors(array $regexpValue): ToolingSettingsIgnoreErrorDto
    {
        if (!isset($regexpValue[static::MESSAGES_KEY], $regexpValue[static::CHECKER_KEY])) {
            throw new InvalidArgumentException(sprintf('Required key [%s][%s|%s] is not found in %s', static::IGNORE_ERRORS_KEY, static::MESSAGES_KEY, static::CHECKER_KEY, $this->toolingFile));
        }

        if (array_filter($regexpValue[static::MESSAGES_KEY], static fn ($value): bool => !is_string($value))) {
            throw new InvalidArgumentException(sprintf('Every value [%s][%s] should be a string is %s', static::IGNORE_ERRORS_KEY, static::MESSAGES_KEY, $this->toolingFile));
        }

        if (!is_string($regexpValue[static::CHECKER_KEY])) {
            throw new InvalidArgumentException(sprintf('Key [%s][%s] should be a string in %s', static::IGNORE_ERRORS_KEY, static::CHECKER_KEY, $this->toolingFile));
        }

        return new ToolingSettingsIgnoreErrorDto($regexpValue[static::MESSAGES_KEY], $regexpValue[static::CHECKER_KEY]);
    }
}
