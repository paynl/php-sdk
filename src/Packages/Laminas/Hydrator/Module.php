<?php

declare(strict_types=1);

namespace PayNL\Sdk\Packages\Laminas\Hydrator;

use PayNL\Sdk\Packages\Laminas\ModuleManager\ModuleManager;

class Module
{
    /**
     * Return default laminas-hydrator configuration for laminas-mvc applications.
     *
     * @return mixed[]
     */
    public function getConfig(): array
    {
        $provider = new ConfigProvider();

        return [
            'service_manager' => $provider->getDependencyConfig(),
        ];
    }

    /**
     * Register a specification for the HydratorManager with the ServiceListener.
     */
    public function init(ModuleManager $moduleManager): void
    {
        $event           = $moduleManager->getEvent();
        $container       = $event->getParam('ServiceManager');
        $serviceListener = $container->get('ServiceListener');

        $serviceListener->addServiceManager(
            'HydratorManager',
            'hydrators',
            HydratorProviderInterface::class,
            'getHydratorConfig'
        );
    }
}
