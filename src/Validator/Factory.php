<?php

declare(strict_types=1);

namespace PayNL\Sdk\Validator;

use PayNL\Sdk\Common\FactoryInterface;
use Psr\Container\ContainerInterface;
use PayNL\Sdk\Hydrator\HydratorAwareInterface;

/**
 * Class Factory
 *
 * @package PayNL\Sdk\Validator
 */
class Factory implements FactoryInterface
{
    /**
     * @inheritDoc
     *
     * @return ValidatorInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName, ?array $options = null): ValidatorInterface
    {
        /** @var ValidatorInterface $validator */
        $validator = new $requestedName();

        /** @phpstan-ignore-next-line */
        if ($validator instanceof HydratorAwareInterface) {
            /** @phpstan-ignore-next-line */
            $validator->setHydrator($container->get('hydratorManager')->get('Entity'));
        }

        return $validator;
    }
}
