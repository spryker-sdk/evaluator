<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\DeadCode;

use SprykerSdk\Evaluator\Checker\AbstractChecker;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Dto\CheckerResponseDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;

class DeadCodeChecker extends AbstractChecker
{
    /**
     * @var string
     */
    public const NAME = 'DEAD_CODE_CHECKER';

    /**
     * @var string
     */
    protected const SOURCE_DIR = 'src';

    protected DeadCodeFinder $deadCodeFinder;

    protected string $checkerDocUrl;

    public function __construct(DeadCodeFinder $deadCodeFinder, string $checkerDocUrl = '')
    {
        $this->deadCodeFinder = $deadCodeFinder;
        $this->checkerDocUrl = $checkerDocUrl;
    }

    public function getName(): string
    {
        return static::NAME;
    }

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
                sprintf('Class "%s" is not used in the project', $class),
                $file,
            );
        }

        return new CheckerResponseDto($violations, $this->checkerDocUrl);
    }
}
