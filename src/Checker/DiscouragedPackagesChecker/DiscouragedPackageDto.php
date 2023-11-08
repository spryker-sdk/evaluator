<?php

namespace SprykerSdk\Evaluator\Checker\DiscouragedPackagesChecker;

class DiscouragedPackageDto
{
    /**
     * @var string
     */
    protected string $packageName;

    /**
     * @var string
     */
    protected string $reason;

    /**
     * @param string $packageName
     * @param string $reason
     */
    public function __construct(string $packageName, string $reason)
    {
        $this->packageName = $packageName;
        $this->reason = $reason;
    }

    /**
     * @return string
     */
    public function getPackageName(): string
    {
        return $this->packageName;
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }
}
