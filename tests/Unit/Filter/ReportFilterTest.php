<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Evaluator\Unit\Filter;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SprykerSdk\Evaluator\Dto\ReportDto;
use SprykerSdk\Evaluator\Dto\ReportLineDto;
use SprykerSdk\Evaluator\Dto\ToolingSettingsDto;
use SprykerSdk\Evaluator\Dto\ToolingSettingsIgnoreErrorDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;
use SprykerSdk\Evaluator\Filter\ReportFilter;

class ReportFilterTest extends TestCase
{
    /**
     * @return void
     */
    public function testFilterReportShouldThrowExceptionWhenInvalidRegexpSet(): void
    {
        // Arrange && Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Invalid regexp/');

        $reportDto = new ReportDto([new ReportLineDto('', [new ViolationDto('message', 'target')])]);
        $toolingSettingsDto = new ToolingSettingsDto([new ToolingSettingsIgnoreErrorDto(['#invalidRegexp'])]);
        $reportFilter = new ReportFilter();

        // Act
        $reportFilter->filterReport($reportDto, $toolingSettingsDto);
    }
}
