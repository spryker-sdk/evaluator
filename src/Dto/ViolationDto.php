<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Dto;

class ViolationDto
{
    /**
     * @var string
     */
    protected string $message;

    /**
     * @var string
     */
    protected string $target;

    /**
     * @param string $message
     * @param string $target E.g. filename, directory, class ...
     */
    public function __construct(string $message, string $target = '')
    {
        $this->message = $message;
        $this->target = $target;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getTarget(): string
    {
        return $this->target;
    }
}
