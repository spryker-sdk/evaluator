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
     * @return array<mixed>
     */
    public function getOrganizationRepositories(string $organization): array;

    public function getRepositoryFileContent(
        string $organization,
        string $repository,
        string $filePath,
        ?string $ref = null
    ): string;
}
