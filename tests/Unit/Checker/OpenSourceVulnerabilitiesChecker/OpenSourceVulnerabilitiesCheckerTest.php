<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Checker\OpenSourceVulnerabilitiesChecker;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Checker\OpenSourceVulnerabilitiesChecker\OpenSourceVulnerabilitiesChecker;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Resolver\PathResolverInterface;
use Symfony\Component\Console\Application;

/**
 * @group SprykerSdkTest
 * @group Evaluator
 * @group Unit
 * @group Checker
 * @group SecurityChecker
 * @group SecurityCheckerTest
 */
class OpenSourceVulnerabilitiesCheckerTest extends TestCase
{
    /**
     * @return void
     */
    public function testReturnInternalError(): void
    {
        $applicationMock = $this->createMock(Application::class);
        // Inject fake process that returns empty output so decoded JSON is invalid
        $fakeProcessFactory = function (string $cwd) {
            return new class
            {
                /**
                 * @return void
                 */
                public function run(): void
                {
                }

                /**
                 * @return string
                 */
                public function getOutput(): string
                {
                    return '';
                }

                /**
                 * @return string
                 */
                public function getErrorOutput(): string
                {
                    return '';
                }

                /**
                 * @return bool
                 */
                public function isSuccessful(): bool
                {
                    return true;
                }
            };
        };
        $securityChecker = new OpenSourceVulnerabilitiesChecker($applicationMock, $this->createPathResolverMock(), '', $fakeProcessFactory);
        $result = $securityChecker->check(new CheckerInputDataDto('/path'));

        $this->assertCount(1, $result->getViolations());
        $this->assertSame('Internal error. Original error: ', $result->getViolations()[0]->getMessage());
        $this->assertSame(OpenSourceVulnerabilitiesChecker::NAME, $result->getViolations()[0]->getTarget());
    }

    /**
     * @return void
     */
    public function testParsesComposerAuditJson(): void
    {
        $applicationMock = $this->createMock(Application::class);
        $json = <<<'JSON'
{
  "advisories": {
    "guzzlehttp/guzzle": [
      {
        "advisoryId": "PKSA-yfw5-9gnj-n2c7",
        "packageName": "guzzlehttp/guzzle",
        "affectedVersions": {},
        "title": "Change in port should be considered a change in origin",
        "cve": "CVE-2022-31091",
        "link": "https://github.com/guzzle/guzzle/security/advisories/GHSA-q559-8m2m-g699"
      }
    ]
  }
}
JSON;

        // Inject fake process that returns the sample JSON on stdout
        $fakeProcessFactory = function (string $cwd) use ($json) {
            return new class ($json)
            {
                private string $out;

                /**
                 * @param string $out
                 */
                public function __construct(string $out)
                {
                    $this->out = $out;
                }

                /**
                 * @return void
                 */
                public function run(): void
                {
                }

                /**
                 * @return string
                 */
                public function getOutput(): string
                {
                    return $this->out;
                }

                /**
                 * @return string
                 */
                public function getErrorOutput(): string
                {
                    return '';
                }

                /**
                 * @return bool
                 */
                public function isSuccessful(): bool
                {
                    return true;
                }
            };
        };

        $securityChecker = new OpenSourceVulnerabilitiesChecker($applicationMock, $this->createPathResolverMock(), '', $fakeProcessFactory);
        $result = $securityChecker->check(new CheckerInputDataDto('/path'));

        $this->assertCount(1, $result->getViolations());
        $this->assertSame('guzzlehttp/guzzle (1 advisories)', $result->getViolations()[0]->getTarget());
        $this->assertStringContainsString('Change in port should be considered a change in origin', $result->getViolations()[0]->getMessage());
        $this->assertStringContainsString('CVE-2022-31091', $result->getViolations()[0]->getMessage());
        $this->assertStringContainsString('https://github.com/guzzle/guzzle/security/advisories/GHSA-q559-8m2m-g699', $result->getViolations()[0]->getMessage());
    }

    /**
     * @return \SprykerSdk\Evaluator\Resolver\PathResolverInterface
     */
    protected function createPathResolverMock(): PathResolverInterface
    {
        $pathResolver = $this->createMock(PathResolverInterface::class);
        $pathResolver->method('getProjectDir')->willReturn('/path');

        return $pathResolver;
    }
}
