<?php

declare(strict_types=1);

namespace PayNL\Sdk\Validator;

use PayNL\Sdk\Common\FactoryInterface;
use Psr\Container\ContainerInterface;
use PayNL\Sdk\Packages\Laminas\Hydrator\HydratorAwareInterface;

/**
 * Class Factory
 *
 * @package PayNL\Sdk\Validator
 */
class Factory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ValidatorInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName, array $options = null): ValidatorInterface
    {
        /** @var ValidatorInterface $validator */
        $validator = new $requestedName();

        if ($validator instanceof HydratorAwareInterface) {
            $validator->setHydrator($container->get('hydratorManager')->get('Entity'));
        }

        return $validator;
    }
}
