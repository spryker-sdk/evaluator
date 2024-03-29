<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto;

use SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;

class ReleaseGroupDto
{
    /**
     * @var \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection
     */
    protected ModuleDtoCollection $moduleCollection;

    /**
     * @var bool
     */
    protected bool $containsProjectChanges;

    /**
     * @var int
     */
    protected int $id;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string
     */
    protected string $link;

    /**
     * @var int
     */
    protected int $rating;

    /**
     * @var string|null
     */
    protected ?string $jiraIssue = null;

    /**
     * @var string|null
     */
    protected ?string $jiraIssueLink = null;

    /**
     * @var bool
     */
    protected bool $hasConflict = false;

    /**
     * @var bool
     */
    protected bool $isSecurity = false;

    /**
     * @param string $name
     * @param \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection $moduleCollection
     * @param bool $containsProjectChanges
     * @param string $link
     * @param bool $hasConflict
     * @param bool $isSecurity
     */
    public function __construct(
        string $name,
        ModuleDtoCollection $moduleCollection,
        bool $containsProjectChanges,
        string $link,
        bool $hasConflict = false,
        bool $isSecurity = false
    ) {
        $this->name = $name;
        $this->link = $link;
        $this->moduleCollection = $moduleCollection;
        $this->containsProjectChanges = $containsProjectChanges;
        $this->hasConflict = $hasConflict;
        $this->isSecurity = $isSecurity;
    }

    /**
     * @return \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection
     */
    public function getModuleCollection(): ModuleDtoCollection
    {
        return $this->moduleCollection;
    }

    /**
     * @param \SprykerSdk\Evaluator\ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection $moduleCollection
     *
     * @return void
     */
    public function setModuleCollection(ModuleDtoCollection $moduleCollection): void
    {
        $this->moduleCollection = $moduleCollection;
    }

    /**
     * @return bool
     */
    public function hasProjectChanges(): bool
    {
        return $this->containsProjectChanges;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @return bool
     */
    public function hasConflict(): bool
    {
        return $this->hasConflict;
    }

    /**
     * @param bool $hasConflict
     *
     * @return void
     */
    public function setHasConflict(bool $hasConflict): void
    {
        $this->hasConflict = $hasConflict;
    }

    /**
     * @return string|null
     */
    public function getJiraIssue(): ?string
    {
        return $this->jiraIssue;
    }

    /**
     * @param string|null $jiraIssue
     *
     * @return void
     */
    public function setJiraIssue(?string $jiraIssue): void
    {
        $this->jiraIssue = $jiraIssue;
    }

    /**
     * @return string|null
     */
    public function getJiraIssueLink(): ?string
    {
        return $this->jiraIssueLink;
    }

    /**
     * @param string|null $jiraIssueLink
     *
     * @return void
     */
    public function setJiraIssueLink(?string $jiraIssueLink): void
    {
        $this->jiraIssueLink = $jiraIssueLink;
    }

    /**
     * @param bool $isSecurity
     *
     * @return void
     */
    public function setIsSecurity(bool $isSecurity): void
    {
        $this->isSecurity = $isSecurity;
    }

    /**
     * @return bool
     */
    public function isSecurity(): bool
    {
        return $this->isSecurity;
    }
}
