<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\PluginsRegistrationWithRestrictionsChecker;

class RestrictionDocBlockValidator
{
    /**
     * @var string
     */
    protected const MESSAGE_INVALID_PATTERN = 'Restriction rule does not match the pattern "%s"';

    /**
     * @var string
     */
    protected const MESSAGE_CLASS_NOT_USED = 'Class "%s" is not used in current dependency provider';

    /**
     * @var string
     */
    protected const RULE_PATTERN = '/^\* - (before|after) \{@link (?<class>.+)\}( .*\.|)$/';

    /**
     * @param string $docBlock
     * @param array<string> $usedClasses
     *
     * @return array<string>
     */
    public function validate(string $docBlock, array $usedClasses): array
    {
        $lines = preg_split('/\R/', $docBlock);

        if ($lines === false) {
            return [];
        }

        return $this->validateLines($lines, $usedClasses);
    }

    /**
     * @param array<string> $lines
     * @param array<string> $usedClasses
     *
     * @return array<string>
     */
    protected function validateLines(array $lines, array $usedClasses): array
    {
        $inRestrictionsBlock = false;
        $violations = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($this->isEmptyLine($line)) {
                continue;
            }

            if ($this->isRestrictionsStartLine($line)) {
                $inRestrictionsBlock = true;

                continue;
            }

            if ($inRestrictionsBlock && $this->isListLine($line)) {
                $violations[] = $this->validateLine($line, $usedClasses);

                continue;
            }

            $inRestrictionsBlock = false;
        }

        return array_filter($violations);
    }

    /**
     * @param string $line
     * @param array<string> $usedClasses
     *
     * @return string|null
     */
    protected function validateLine(string $line, array $usedClasses): ?string
    {
        if (!preg_match(static::RULE_PATTERN, $line, $matches)) {
            return sprintf(static::MESSAGE_INVALID_PATTERN, static::RULE_PATTERN);
        }

        if (!in_array($matches['class'], $usedClasses, true)) {
            return sprintf(static::MESSAGE_CLASS_NOT_USED, $matches['class']);
        }

        return null;
    }

    /**
     * @param string $line
     *
     * @return bool
     */
    protected function isListLine(string $line): bool
    {
        return (bool)preg_match('/^\* +-/', $line);
    }

    /**
     * @param string $line
     *
     * @return bool
     */
    protected function isRestrictionsStartLine(string $line): bool
    {
        return (bool)preg_match('/^\* +Restrictions:/', $line);
    }

    /**
     * @param string $line
     *
     * @return bool
     */
    protected function isEmptyLine(string $line): bool
    {
        return $line === '*';
    }
}
