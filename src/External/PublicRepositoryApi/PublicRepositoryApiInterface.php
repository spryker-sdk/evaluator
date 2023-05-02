<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\External\PublicRepositoryApi;

interface PublicRepositoryApiInterface
{
    /**
     * @param string $organization
     *
     * @return array<mixed>
     */
    public function getOrganizationRepositories(string $organization): array;

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
    ): string;
}
