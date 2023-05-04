<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\ValidProject\MultidimensionalArray\Application;

use Spryker\Zed\ErrorHandler\Communication\Plugin\Application\GlossaryStorageConfig;
use Spryker\Zed\EventDispatcher\Communication\Plugin\Application\GlossaryKeyDeletePublisherPlugin;
use Spryker\Zed\EventDispatcher\Communication\Plugin\Application\GlossaryKeyWriterPublisherPlugin;
use Spryker\Zed\EventDispatcher\Communication\Plugin\Application\GlossaryTranslationWritePublisherPlugin;

class ApplicationDependencyProvider
{
    /**
     * @return array<array>
     */
    protected function getPlugins(): array
    {
        return [ // 1st level
            GlossaryStorageConfig::PUBLISH_TRANSLATION => [ // 2nd level
                'delete' => [ // 3rd level. Only plugins are allowed to be here.
                    new GlossaryKeyDeletePublisherPlugin(),
                ],
                'write' => [
                    new GlossaryKeyWriterPublisherPlugin(),
                    new GlossaryTranslationWritePublisherPlugin(),
                ],
            ],
        ];
    }
}
