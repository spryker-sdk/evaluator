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

class DeployYamlFilesPhpVersionStrategy implements PhpVersionCheckerStrategyInterface
{
    /**
     * @var string
     */
    public const MESSAGE_YAML_PATH_NOT_FOUND = 'Image tag path "%s" not found in deploy file';

    /**
     * @var string
     */
    public const MESSAGE_USED_NOT_ALLOWED_PHP_VERSION = "Deploy file uses not allowed PHP image version \"%s\"\nImage tag must contain allowed PHP version (image:abc-8.0)";

    /**
     * @var \SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy\FileReader\DeploymentYamlFileReader
     */
    protected DeploymentYamlFileReader $deploymentYamlFileReader;

    /**
     * @param \SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategy\FileReader\DeploymentYamlFileReader $deploymentYamlFileReader
     */
    public function __construct(DeploymentYamlFileReader $deploymentYamlFileReader)
    {
        $this->deploymentYamlFileReader = $deploymentYamlFileReader;
    }

    /**
     * @param array<string> $allowedPhpVersions
     * @param string $path
     *
     * @return \SprykerSdk\Evaluator\Checker\PhpVersionChecker\CheckerStrategyResponse
     */
    public function check(array $allowedPhpVersions, string $path): CheckerStrategyResponse
    {
        $responses = [];

        $fileIterator = $this->deploymentYamlFileReader->read($this->getTarget($path));

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
            return new CheckerStrategyResponse([], [new ViolationDto(sprintf(static::MESSAGE_YAML_PATH_NOT_FOUND, '[image][tag]'), $fileName)]);
        }

        $validVersions = array_filter(
            $allowedPhpVersions,
            static fn (string $version): bool => (bool)preg_match(
                sprintf('/[^\d.]%s/', str_replace('.', '\.', $version)),
                $deployStructure['image']['tag'],
            )
        );

        if (count($validVersions) === 0) {
            return new CheckerStrategyResponse([], [new ViolationDto(sprintf(static::MESSAGE_USED_NOT_ALLOWED_PHP_VERSION, $deployStructure['image']['tag']), $fileName)]);
        }

        return new CheckerStrategyResponse($validVersions, []);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function getTarget(string $path): string
    {
        return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'deploy**.yml';
    }
}
