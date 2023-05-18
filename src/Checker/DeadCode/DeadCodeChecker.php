<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\DeadCode;

use SprykerSdk\Evaluator\Checker\CheckerInterface;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Dto\CheckerResponseDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;

class DeadCodeChecker implements CheckerInterface
{
    /**
     * @var string
     */
    public const NAME = 'DEAD_CODE_CHECKER';

    /**
     * @var string
     */
    protected const SOURCE_DIR = 'src';

    /**
     * @var \SprykerSdk\Evaluator\Checker\DeadCode\DeadCodeFinder
     */
    protected DeadCodeFinder $deadCodeFinder;

    /**
     * @var string
     */
    protected string $checkerDocUrl;

    /**
     * @param \SprykerSdk\Evaluator\Checker\DeadCode\DeadCodeFinder $deadCodeFinder
     * @param string $checkerDocUrl
     */
    public function __construct(DeadCodeFinder $deadCodeFinder, string $checkerDocUrl = '')
    {
        $this->deadCodeFinder = $deadCodeFinder;
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
        $violations = [];
        $path = $inputData->getPath();
        if (strpos($path, DIRECTORY_SEPARATOR . static::SOURCE_DIR . DIRECTORY_SEPARATOR) === false) {
            $sourceDir = $path . DIRECTORY_SEPARATOR . static::SOURCE_DIR;
            if (!file_exists($sourceDir)) {
                return new CheckerResponseDto([], $this->checkerDocUrl);
            }

            $path = $sourceDir;
        }

        foreach ($this->deadCodeFinder->find($path) as $class => $file) {
            $violations[] = new ViolationDto(
                sprintf('Class "%s" is not used in project', $class),
                $file,
            );
        }

        return new CheckerResponseDto($violations, $this->checkerDocUrl);
    }
}
