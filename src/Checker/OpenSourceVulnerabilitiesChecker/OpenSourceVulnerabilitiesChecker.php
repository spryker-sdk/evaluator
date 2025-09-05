<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\OpenSourceVulnerabilitiesChecker;

use SprykerSdk\Evaluator\Checker\AbstractChecker;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Dto\CheckerResponseDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;
use SprykerSdk\Evaluator\Resolver\PathResolverInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Process\Process;

class OpenSourceVulnerabilitiesChecker extends AbstractChecker
{
    /**
     * @var string
     */
    public const NAME = 'OPEN_SOURCE_VULNERABILITIES_CHECKER';

    /**
     * @var string
     */
    protected const NOT_AVAILABLE = 'n/a';

    /**
     * @var \Symfony\Component\Console\Application
     */
    protected Application $application;

    /**
     * @var \SprykerSdk\Evaluator\Resolver\PathResolverInterface
     */
    protected PathResolverInterface $pathResolver;

    /**
     * @var string
     */
    protected string $checkerDocUrl;

    /**
     * @param \Symfony\Component\Console\Application $application
     * @param \SprykerSdk\Evaluator\Resolver\PathResolverInterface $pathResolver
     * @param string $checkerDocUrl
     */
    public function __construct(Application $application, PathResolverInterface $pathResolver, string $checkerDocUrl = '')
    {
        $this->application = $application;
        $this->pathResolver = $pathResolver;
        $this->checkerDocUrl = $checkerDocUrl;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * @param \SprykerSdk\Evaluator\Dto\CheckerInputDataDto $inputData
     *
     * @return \SprykerSdk\Evaluator\Dto\CheckerResponseDto
     */
    public function check(CheckerInputDataDto $inputData): CheckerResponseDto
    {
        $process = new Process([
            'composer',
            'audit',
            '--format=json',
            '--abandoned=ignore',
            '--no-interaction',
            '--no-ansi',
        ]);
        $process->setWorkingDirectory($this->pathResolver->getProjectDir());
        $process->run();

        $rawViolations = $process->getOutput();
        if ($rawViolations === '' && !$process->isSuccessful()) {
            $rawViolations = $process->getErrorOutput();
        }

        // TEMP: override with test JSON to validate output formatting
        $rawViolations = <<<'JSON'
{
  "advisories": {
    "guzzlehttp/guzzle": [
      {
        "advisoryId": "PKSA-yfw5-9gnj-n2c7",
        "packageName": "guzzlehttp/guzzle",
        "affectedVersions": {},
        "title": "Change in port should be considered a change in origin",
        "cve": "CVE-2022-31091",
        "link": "https://github.com/guzzle/guzzle/security/advisories/GHSA-q559-8m2m-g699",
        "reportedAt": {
          "date": "2022-06-20 22:24:00.000000",
          "timezone_type": 3,
          "timezone": "UTC"
        },
        "sources": [
          {
            "name": "GitHub",
            "remoteId": "GHSA-q559-8m2m-g699"
          }
        ]
      }
    ],
    "guzzlehttp/guzzlee": [
      {
        "advisoryId": "PKSA-yfw5-9gnj-n2c7",
        "packageName": "guzzlehttp/guzzle",
        "affectedVersions": {},
        "title": "Change in port should be considered a change in origin",
        "cve": "CVE-2022-31091",
        "link": "https://github.com/guzzle/guzzle/security/advisories/GHSA-q559-8m2m-g699",
        "reportedAt": {
          "date": "2022-06-20 22:24:00.000000",
          "timezone_type": 3,
          "timezone": "UTC"
        },
        "sources": [
          {
            "name": "GitHub",
            "remoteId": "GHSA-q559-8m2m-g699"
          }
        ]
      }
    ]
  }
}
JSON;
        $decoded = json_decode($rawViolations, true);

        if (!is_array($decoded)) {
            $violationDto = new ViolationDto(
                "Internal error. Original error: $rawViolations",
                static::NAME,
            );

            return new CheckerResponseDto([$violationDto], $this->checkerDocUrl);
        }

        $advisories = $decoded['advisories'] ?? [];
        if (!is_array($advisories) || $advisories === []) {
            return new CheckerResponseDto([], $this->checkerDocUrl);
        }

        $violationMessages = [];
        foreach ($advisories as $package => $packageAdvisories) {
            $advisoryList = is_array($packageAdvisories) ? $packageAdvisories : [];
            $subject = sprintf('%s (%d advisories)', $package, count($advisoryList));

            $violationMessages[] = new ViolationDto(
                $this->createSecurityAdvisoryMessage($advisoryList),
                $subject,
            );
        }

        return new CheckerResponseDto($violationMessages, $this->checkerDocUrl);
    }

    /**
     * @param array<int, array<string, string>> $advisories
     *
     * @return string
     */
    protected function createSecurityAdvisoryMessage(array $advisories): string
    {
        $messages = [];

        foreach ($advisories as $advisory) {
            $messages[] = sprintf(
                '%s (%s): %s',
                $advisory['title'] ?? static::NOT_AVAILABLE,
                $advisory['cve'] ?? static::NOT_AVAILABLE,
                $advisory['link'] ?? '',
            );
        }

        return implode(PHP_EOL, $messages);
    }
}
