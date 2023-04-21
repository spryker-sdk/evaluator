<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy;

use SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy\FileReader\DeploymentYamlFileReader;
use SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategyResponse;
use SprykerSdk\Evaluator\Dto\ViolationDto;
use SprykerSdk\Evaluator\Resolver\PathResolverInterface;

class DeployYamlFilesPhpVersionStrategy implements PhpVersionCheckerStrategyInterface
{
    /**
     * @var string
     */
    public const MESSAGE_YAML_PATH_NOT_FOUND = 'Image tag path not found in deploy file "%s"';

    /**
     * @var string
     */
    public const MESSAGE_USED_NOT_ALLOWED_PHP_VERSION = 'Deploy file "%s" used not allowed php version';

    /**
     * @var \SprykerSdk\Evaluator\Resolver\PathResolverInterface
     */
    protected PathResolverInterface $pathResolver;

    /**
     * @var \SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy\FileReader\DeploymentYamlFileReader
     */
    protected DeploymentYamlFileReader $deploymentYamlFileReader;

    /**
     * @param \SprykerSdk\Evaluator\Resolver\PathResolverInterface $pathResolver
     * @param \SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy\FileReader\DeploymentYamlFileReader $deploymentYamlFileReader
     */
    public function __construct(PathResolverInterface $pathResolver, DeploymentYamlFileReader $deploymentYamlFileReader)
    {
        $this->pathResolver = $pathResolver;
        $this->deploymentYamlFileReader = $deploymentYamlFileReader;
    }

    /**
     * @param array<string> $allowedPhpVersions
     *
     * @return \SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategyResponse
     */
    public function check(array $allowedPhpVersions): CheckerStrategyResponse
    {
        $responses = [];

        $fileIterator = $this->deploymentYamlFileReader->read($this->getTarget());

        foreach ($fileIterator as $fileName => $deploymentStructure) {
            $responses[] = $this->checkDeployFile($fileName, $deploymentStructure, $allowedPhpVersions);
        }

        if (count($responses) === 0) {
            // skip
            return new CheckerStrategyResponse($allowedPhpVersions, []);
        }

        $usedVersions = array_map(static fn (CheckerStrategyResponse $response): array => $response->getUsedVersions(), $responses);

        $commonPhpVersions = count($usedVersions) === 1 ? $usedVersions[0] : array_intersect(...$usedVersions);

        /** @var array<array<\SprykerSdk\Evaluator\Dto\ViolationDto>> $violations */
        $violations = array_map(static fn (CheckerStrategyResponse $response): array => $response->getViolations(), $responses);

        return new CheckerStrategyResponse($commonPhpVersions, array_merge(...$violations));
    }

    /**
     * @param string $fileName
     * @param array<mixed> $deployStructure
     * @param array<string> $allowedPhpVersions
     *
     * @return \SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategyResponse
     */
    protected function checkDeployFile(string $fileName, array $deployStructure, array $allowedPhpVersions): CheckerStrategyResponse
    {
        if (!isset($deployStructure['image']['tag'])) {
            return new CheckerStrategyResponse([], [new ViolationDto(sprintf(static::MESSAGE_YAML_PATH_NOT_FOUND, $fileName))]);
        }

        $validVersions = array_filter(
            $allowedPhpVersions,
            static fn (string $version): bool => (bool)preg_match(
                sprintf('/[^\d.]%s/', str_replace('.', '\.', $version)),
                $deployStructure['image']['tag'],
            )
        );

        if (count($validVersions) === 0) {
            return new CheckerStrategyResponse([], [new ViolationDto(sprintf(static::MESSAGE_USED_NOT_ALLOWED_PHP_VERSION, $fileName))]);
        }

        return new CheckerStrategyResponse($validVersions, []);
    }

    /**
     * @return string
     */
    public function getTarget(): string
    {
        return $this->pathResolver->resolvePath() . '/deploy**.yml';
    }
}
