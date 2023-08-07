<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker;

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
     * @return array<\SprykerSdk\Evaluator\Checker\CheckerInterface>
     */
    public function getAllCheckers(): array
    {
        return $this->checkers;
    }
}
