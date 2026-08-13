<?php

declare(strict_types=1);

namespace PayNL\Sdk\Hydrator;

use DateTime as stdDateTime;
use PayNL\Sdk\{
    Common\DateTime,
    Common\DebugAwareInterface,
    Common\DebugAwareTrait,
    Exception\UnexpectedValueException,
    Hydrator\Manager as HydratorManager,
    Model\Manager as ModelManager,
    Validator\ValidatorManagerAwareInterface,
    Validator\ValidatorManagerAwareTrait
};
use Exception;
use ReflectionMethod;
use Throwable;

/**
 * Class AbstractHydrator
 *
 * @package PayNL\Sdk\Hydrator
 */

abstract class AbstractHydrator implements DebugAwareInterface, ValidatorManagerAwareInterface
{
    use DebugAwareTrait;
    use ValidatorManagerAwareTrait;

    /**
     * @var HydratorManager
     */
    protected $hydratorManager;

    /**
     * @var ModelManager
     */
    protected $modelManager;

    /**
     * AbstractHydrator constructor.
     *
     * @param HydratorManager $hydratorManager
     * @param ModelManager    $modelManager
     */
    public function __construct(HydratorManager $hydratorManager, ModelManager $modelManager)
    {
        $this->hydratorManager = $hydratorManager;
        $this->modelManager = $modelManager;
    }

    /**
     * @internal also automatically sets links and filters to remove all null values
     */
    public function hydrate(array $data, $object)
    {
        $data = array_filter($data, static function ($item) {
            return null !== $item;
        });

        foreach ($data as $key => $value) {
            $setter = 'set' . ucfirst((string)$key);
            if (method_exists($object, $setter) === false) {
                continue;
            }

            $method = new ReflectionMethod($object, $setter);
            if ($method->isPublic() === false || $method->getNumberOfRequiredParameters() > 1) {
                continue;
            }

            try {
                $object->$setter($value);
            } catch (Throwable $throwable) {
                throw new UnexpectedValueException(
                    sprintf('Unable to hydrate "%s::%s"', get_class($object), $setter),
                    500,
                    $throwable
                );
            }
        }

        return $object;
    }

    public function extract($object): array
    {
        $data = [];
        foreach (get_class_methods($object) as $methodName) {
            if (str_starts_with($methodName, 'get') === true) {
                $key = lcfirst(substr($methodName, 3));
            } elseif (str_starts_with($methodName, 'is') === true) {
                $key = lcfirst(substr($methodName, 2));
            } else {
                continue;
            }

            $method = new ReflectionMethod($object, $methodName);
            if (
                $method->isPublic() === false
                || $method->getNumberOfRequiredParameters() > 0
                || $methodName === 'getIterator'
            ) {
                continue;
            }

            try {
                $data[$key] = $object->$methodName();
            } catch (Throwable) {
                continue;
            }
        }

        return array_filter($data, static function ($item) {
            return null !== $item;
        });
    }

    /**
     * @param string|stdDateTime $dateTime
     *
     * @throws Exception
     * @return DateTime|null
     *
     */
    protected function getSdkDateTime($dateTime): ?DateTime
    {
        if ($dateTime instanceof DateTime) {
            return $dateTime;
        }

        if ($dateTime instanceof stdDateTime) {
            $dateTime = $dateTime->format(stdDateTime::ATOM);
        }

        if ($dateTime === '') {
            return null;
        }

        return DateTime::createFromFormat(DateTime::ATOM, $dateTime) ?: null;
    }
}
