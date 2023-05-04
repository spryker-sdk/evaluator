<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\External\PublicRepositoryApi;

use SprykerSdk\Evaluator\External\Http\HttpClientFactoryInterface;

class GitHubPublicRepositoryApi implements PublicRepositoryApiInterface
{
    /**
     * @var array<string, string>
     */
    protected const API_VERSION_HEADER = ['X-GitHub-Api-Version' => '2022-11-28'];

    /**
     * @var array<string, string>
     */
    protected const JSON_ACCEPT_HEADER = ['Accept' => 'application/vnd.github+json'];

    /**
     * @var array<string, string>
     */
    protected const RAW_ACCEPT_HEADER = ['Accept' => 'application/vnd.github.raw'];

    /**
     * @var \SprykerSdk\Evaluator\External\Http\HttpClientFactoryInterface
     */
    protected HttpClientFactoryInterface $httpClientFactory;

    /**
     * @var string|null
     */
    protected ?string $githubAuth;

    /**
     * @param \SprykerSdk\Evaluator\External\Http\HttpClientFactoryInterface $httpClientFactory
     * @param string|null $githubAuth
     */
    public function __construct(HttpClientFactoryInterface $httpClientFactory, ?string $githubAuth = null)
    {
        $this->httpClientFactory = $httpClientFactory;
        $this->githubAuth = $githubAuth;
    }

    /**
     * @param string $organization
     *
     * @return array<mixed>
     */
    public function getOrganizationRepositories(string $organization): array
    {
        $repositories = [];
        $page = 1;

        do {
            $client = $this->httpClientFactory->createClient();
            $response = $client->request(
                'GET',
                sprintf('https://api.github.com/orgs/%s/repos', $organization),
                [
                    'headers' => array_merge(static::API_VERSION_HEADER, static::JSON_ACCEPT_HEADER, $this->getAuthHeader()),
                    'query' => ['page' => $page],
                ],
            );
            $items = json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);
            $repositories[] = $items;
            ++$page;
        } while (count($items) > 0);

        return array_merge(...$repositories);
    }

    /**
     * @param string $organization
     * @param string $repository
     * @param string $filePath
     * @param string|null $ref
     *
     * @return string
     */
    public function getRepositoryFileContent(
        string $organization,
        string $repository,
        string $filePath,
        ?string $ref = null
    ): string {
        $client = $this->httpClientFactory->createClient();
        $response = $client->request(
            'GET',
            sprintf('https://api.github.com/repos/%s/%s/contents/%s', $organization, $repository, $filePath),
            [
                'headers' => array_merge(static::API_VERSION_HEADER, static::RAW_ACCEPT_HEADER, $this->getAuthHeader()),
                'query' => $ref !== null ? ['ref' => $ref] : [],
            ],
        );

        return (string)$response->getBody();
    }

    /**
     * @return array<string, string>
     */
    protected function getAuthHeader(): array
    {
        return $this->githubAuth ? ['Authorization' => sprintf('Bearer %s', $this->githubAuth)] : [];
    }
}
