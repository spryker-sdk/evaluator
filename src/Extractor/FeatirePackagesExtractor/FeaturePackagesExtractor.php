<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Extractor\FeatirePackagesExtractor;

use GuzzleHttp\Exception\ClientException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use SprykerSdk\Evaluator\External\PublicRepositoryApi\PublicRepositoryApiInterface;
use Symfony\Component\Filesystem\Filesystem;

class FeaturePackagesExtractor implements FeaturePackagesExtractorInterface
{
    /**
     * @var string
     */
    protected const ORGANIZATION_NAME = 'spryker-feature';

    /**
     * @var string
     */
    protected const COMPOSER_JSON_FILE = 'composer.json';

    /**
     * @var \SprykerSdk\Evaluator\External\PublicRepositoryApi\PublicRepositoryApiInterface
     */
    private PublicRepositoryApiInterface $publicRepositoryApi;

    /**
     * @var string
     */
    private string $targetTag;

    /**
     * @var \SprykerSdk\Evaluator\Extractor\FeatirePackagesExtractor\FeaturePackageCollector
     */
    private FeaturePackageCollector $featurePackageCollector;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var string
     */
    private string $dataFile;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @param \SprykerSdk\Evaluator\External\PublicRepositoryApi\PublicRepositoryApiInterface $publicRepositoryApi
     * @param string $targetTag
     * @param \SprykerSdk\Evaluator\Extractor\FeatirePackagesExtractor\FeaturePackageCollector $featurePackageCollector
     * @param \Psr\Log\LoggerInterface $logger
     * @param string $dataFile
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(
        PublicRepositoryApiInterface $publicRepositoryApi,
        string $targetTag,
        FeaturePackageCollector $featurePackageCollector,
        LoggerInterface $logger,
        string $dataFile,
        Filesystem $filesystem
    ) {
        $this->publicRepositoryApi = $publicRepositoryApi;
        $this->targetTag = $targetTag;
        $this->featurePackageCollector = $featurePackageCollector;
        $this->logger = $logger;
        $this->dataFile = $dataFile;
        $this->filesystem = $filesystem;
    }

    /**
     * @return void
     */
    public function extract(): void
    {
        $this->logger->info(sprintf('Start fetching the "%s" repository list', static::ORGANIZATION_NAME));

        $repositories = $this->getFeatureRepositories();

        $this->logger->info(sprintf('Fetched %s feature repositories', count($repositories)));

        $collectedPackages = [];

        foreach ($this->getFeatureRepositories() as $repository) {
            $collectedPackages = $this->collectPackagesFromRepository($repository, $collectedPackages);
        }

        $this->filesystem->dumpFile($this->dataFile, json_encode($collectedPackages, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES));
    }

    /**
     * @param string $repository
     * @param array<string, string> $collectedPackages
     *
     * @return array<string, string>
     */
    protected function collectPackagesFromRepository(string $repository, array $collectedPackages): array
    {
        $this->logger->info(sprintf('===> Start processing %s/%s"...', static::ORGANIZATION_NAME, $repository));

        try {
            $composerFileContent = json_decode(
                $this->publicRepositoryApi->getRepositoryFileContent(
                    static::ORGANIZATION_NAME,
                    $repository,
                    static::COMPOSER_JSON_FILE,
                    $this->targetTag,
                ),
                true,
                512,
                \JSON_THROW_ON_ERROR,
            );
        } catch (ClientException $e) {
            $this->logger->warning($e->getMessage());

            return $collectedPackages;
        }

        $collectedPackages = $this->featurePackageCollector->collect(
            $collectedPackages,
            array_merge($composerFileContent['require-dev'] ?? [], $composerFileContent['require']),
        );

        $this->logger->info('<=== Processed');

        return $collectedPackages;
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @return array<string>
     */
    protected function getFeatureRepositories(): array
    {
        $repositories = $this->publicRepositoryApi->getOrganizationRepositories(static::ORGANIZATION_NAME);

        $repositoryNames = [];

        foreach ($repositories as $repository) {
            if (!isset($repository['name'])) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Key `name` is not found in repository response %s',
                        json_encode($repository, \JSON_THROW_ON_ERROR),
                    ),
                );
            }

            $repositoryNames[] = $repository['name'];
        }

        return $repositoryNames;
    }
}
