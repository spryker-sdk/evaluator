<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\PhpVersionChecker;

use SprykerSdk\Evaluator\Checker\CheckerInterface;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;
use SprykerSdk\Evaluator\Resolver\PathResolverInterface;

class PhpVersionChecker implements CheckerInterface
{
    /**
     * @var string
     */
    public const NAME = 'PHP_VERSION_CHECKER';

    /**
     * @var string
     */
    public const MESSAGE_INCONSISTENT_PHP_VERSIONS = 'Not all the targets have same PHP versions';

    /**
     * @var \SprykerSdk\Evaluator\Resolver\PathResolverInterface
     */
    protected PathResolverInterface $pathResolver;

    /**
     * @var array<string>
     */
    protected array $allowedPhpVersions;

    /**
     * @var array<\SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy\PhpVersionCheckerStrategyInterface>
     */
    protected array $checkerStrategies;

    /**
     * @param \SprykerSdk\Evaluator\Resolver\PathResolverInterface $pathResolver
     * @param array<string> $allowedPhpVersions
     * @param array<\SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy\PhpVersionCheckerStrategyInterface> $checkerStrategies
     */
    public function __construct(PathResolverInterface $pathResolver, array $allowedPhpVersions, array $checkerStrategies)
    {
        $this->pathResolver = $pathResolver;
        $this->allowedPhpVersions = $allowedPhpVersions;
        $this->checkerStrategies = $checkerStrategies;
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\CheckerInputDataDto $inputData
     *
     * @return array<\SprykerSdk\Evaluator\Dto\ViolationDto>
     */
    public function check(CheckerInputDataDto $inputData): array
    {
        $violations = [];
        $usedVersions = [];

        foreach ($this->checkerStrategies as $checkerStrategy) {
            $response = $checkerStrategy->check($this->allowedPhpVersions, $this->pathResolver->getProjectDir());

            $violations[] = $response->getViolations();
            $usedVersions[$checkerStrategy->getTarget($this->pathResolver->getProjectDir())] = $response->getUsedVersions();
        }

        $violations[] = $this->checkConsistency($usedVersions);

        return array_merge(...$violations);
    }

    /**
     * @param array<string, array<string>> $usedVersions
     *
     * @return array<\SprykerSdk\Evaluator\Dto\ViolationDto>
     */
    protected function checkConsistency(array $usedVersions): array
    {
        $versions = array_values($usedVersions);

        $commonPhpVersions = count($usedVersions) > 1
            ? array_intersect(...$versions)
            : $versions[0] ?? [];

        return count($commonPhpVersions) === 0
            ? [new ViolationDto(static::MESSAGE_INCONSISTENT_PHP_VERSIONS, $this->createConsistencyViolationTarget($usedVersions))]
            : [];
    }

    /**
     * @param array<string, array<string>> $usedVersions
     *
     * @return string
     */
    protected function createConsistencyViolationTarget(array $usedVersions): string
    {
        $messages = [];

        foreach ($usedVersions as $target => $versions) {
            $messages[] = sprintf(
                '%s: %s',
                $target,
                count($versions)
                    ? implode(', ', array_map(static fn (string $version): string => sprintf('php%s', $version), $versions))
                    : '-',
            );
        }

        return implode(PHP_EOL, $messages);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
    }
}
