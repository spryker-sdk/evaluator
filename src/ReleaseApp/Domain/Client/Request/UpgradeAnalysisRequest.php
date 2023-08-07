<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Domain\Client\Request;

use SprykerSdk\Evaluator\ReleaseApp\Domain\Entities\UpgradeAnalysis;

class UpgradeAnalysisRequest implements RequestInterface
{
    /**
     * @var string
     */
    protected const PROJECT_NAME_KEY = 'projectName';

    /**
     * @var string
     */
    protected const COMPOSER_JSON_KEY = 'composerJson';

    /**
     * @var string
     */
    protected const COMPOSER_LOCK_KEY = 'composerLock';

    /**
     * @var string
     */
    protected string $projectName;

    /**
     * @var array<mixed>
     */
    protected array $composerJson;

    /**
     * @var array<mixed>
     */
    protected array $composerLock;

    /**
     * @param string $projectName
     * @param array<mixed> $composerJson
     * @param array<mixed> $composerLock
     */
    public function __construct(string $projectName, array $composerJson, array $composerLock)
    {
        $this->projectName = $projectName;
        $this->composerJson = $composerJson;
        $this->composerLock = $composerLock;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        $json_encode = (string)json_encode($this->getBodyArray());

        file_put_contents('./request.json', $json_encode);

        return $json_encode;
    }

    /**
     * @return string
     */
    public function getResponseClass(): string
    {
        return UpgradeAnalysis::class;
    }

    /**
     * @return string|null
     */
    public function getParameters(): ?string
    {
        return null;
    }

    /**
     * @return array<mixed>
     */
    protected function getBodyArray(): array
    {
        $composerJsonContent = json_encode($this->composerJson);
        $composerLockContent = json_encode($this->composerLock);

        return [
            static::PROJECT_NAME_KEY => $this->projectName,
            static::COMPOSER_JSON_KEY => $composerJsonContent,
            static::COMPOSER_LOCK_KEY => $composerLockContent,
        ];
    }
}
