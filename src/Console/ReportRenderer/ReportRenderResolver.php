<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Console\ReportRenderer;

use InvalidArgumentException;

class ReportRenderResolver
{
    /**
     * @var array<\SprykerSdk\Evaluator\Console\ReportRenderer\ReportRendererInterface>
     */
    protected array $reportRenderers;

    /**
     * @param array<\SprykerSdk\Evaluator\Console\ReportRenderer\ReportRendererInterface> $reportRenderers
     */
    public function __construct(array $reportRenderers)
    {
        $this->reportRenderers = $reportRenderers;
    }

    /**
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return \SprykerSdk\Evaluator\Console\ReportRenderer\ReportRendererInterface
     */
    public function resolve(string $name): ReportRendererInterface
    {
        $formats = [];
        foreach ($this->reportRenderers as $reportRenderer) {
            if ($reportRenderer->getName() === $name) {
                return $reportRenderer;
            }
            $formats[] = $reportRenderer->getName();
        }

        throw new InvalidArgumentException(
            sprintf(
                'Unsupported format `%s`. Available formats: `%s`',
                $name,
                implode('`, `', $formats),
            ),
        );
    }
}
