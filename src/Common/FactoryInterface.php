<?php

declare(strict_types=1);

namespace PayNL\Sdk\Common;

use Psr\Container\ContainerInterface;

/**
 * Interface FactoryInterface
 *
 * @package PayNL\Sdk\Factory
 */
interface FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @throws \PayNL\Sdk\Exception\ServiceNotFoundException When unable to resolve the service.
     * @throws \PayNL\Sdk\Exception\ServiceNotCreatedException When an exception occurs during the service creation.
     *
     * @return object
     */
    public function __invoke(ContainerInterface $container, string $requestedName, ?array $options = null);
}
