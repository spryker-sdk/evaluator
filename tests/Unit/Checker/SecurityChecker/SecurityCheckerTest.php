<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Checker\SecurityChecker;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Checker\SecurityChecker\SecurityChecker;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Resolver\PathResolverInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @group SprykerSdkTest
 * @group Evaluator
 * @group Unit
 * @group Checker
 * @group SecurityChecker
 * @group SecurityCheckerTest
 */
class SecurityCheckerTest extends TestCase
{
    /**
     * @return void
     */
    public function testReturnInternalError(): void
    {
        $applicationMock = $this->createMock(Application::class);
        $applicationMock->expects($this->once())
            ->method('run')
            ->with(
                new ArrayInput([
                    'command' => 'security:check',
                    '--path' => '/path',
                    '--format' => 'json',
                ]),
                new BufferedOutput(),
            );
        $securityChecker = new SecurityChecker($applicationMock, $this->createPathResolverMock());
        $result = $securityChecker->check(new CheckerInputDataDto('/path'));

        $this->assertCount(1, $result->getViolations());
        $this->assertSame('Internal error. Original error: ', $result->getViolations()[0]->getMessage());
        $this->assertSame(SecurityChecker::NAME, $result->getViolations()[0]->getTarget());
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
