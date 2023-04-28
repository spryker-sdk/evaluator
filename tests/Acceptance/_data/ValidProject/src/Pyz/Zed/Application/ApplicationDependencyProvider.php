<?php


declare(strict_types=1);

namespace ValidProject\src\Pyz\Zed\Application;

use Spryker\Zed\ErrorHandler\Communication\Plugin\Application\GlossaryStorageConfig;
use Spryker\Zed\EventDispatcher\Communication\Plugin\Application\GlossaryKeyDeletePublisherPlugin;
use Spryker\Zed\EventDispatcher\Communication\Plugin\Application\GlossaryKeyWriterPublisherPlugin;
use Spryker\Zed\EventDispatcher\Communication\Plugin\Application\GlossaryTranslationWritePublisherPlugin;

class ApplicationDependencyProvider
{
    /**
     * @evaluator-skip-multidimensional-array
     *
     * @return array<array>
     */
    protected function getListPlugins(): array
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

    /**
     * @return array
     */
    protected function getPublisherPlugins(): array
    {
        $productSearchPlugins = [
            new MerchantMerchantProductSearchWritePublisherPlugin(),
            new MerchantProductSearchWritePublisherPlugin(),
        ];

        return array_merge(
            [
                PublishAndSynchronizeHealthCheckConfig::PUBLISH_PUBLISH_AND_SYNCHRONIZE_HEALTH_CHECK => $this->getHealthCheckPublisherPlugins(),
            ],
            $productSearchPlugins,
            $this->getPlugins(),
        );
    }

    /**
     * @return array<array>
     */
    protected function getPlugins(): array
    {
        return [ // 1st level
            GlossaryStorageConfig::PUBLISH_TRANSLATION => [ // 2nd level
                new GlossaryKeyDeletePublisherPlugin(),
                new GlossaryKeyWriterPublisherPlugin(),
                new GlossaryTranslationWritePublisherPlugin(),
            ],
        ];
    }
}
