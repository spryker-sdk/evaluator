<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker;

use InvalidArgumentException;
use Traversable;

class CheckerRegistry implements CheckerRegistryInterface
{
    /**
     * @var array<\SprykerSdk\Evaluator\Checker\CheckerInterface>
     */
    protected array $checkers;

    /**
     * @param iterable<\SprykerSdk\Evaluator\Checker\CheckerInterface> $checkers
     */
    public function __construct(iterable $checkers)
    {
        $this->checkers = $checkers instanceof Traversable ? iterator_to_array($checkers) : $checkers;
    }

    /**
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return \SprykerSdk\Evaluator\Checker\CheckerInterface
     */
    public function getCheckerByName(string $name): CheckerInterface
    {
        foreach ($this->checkers as $checker) {
            if ($checker->getName() === $name) {
                return $checker;
            }
        }

        throw new InvalidArgumentException(sprintf('Checker `%s` is not found', $name));
    }

    /**
     * @return array<\SprykerSdk\Evaluator\Checker\CheckerInterface>
     */
    public function getAllCheckers(): array
    {
        return $this->checkers;
    }
}
