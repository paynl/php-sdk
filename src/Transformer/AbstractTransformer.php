<?php

declare(strict_types=1);

namespace PayNL\Sdk\Transformer;

use PayNL\Sdk\Exception\UnexpectedValueException;
use PayNL\Sdk\Hydrator\AbstractHydrator;
use PayNL\Sdk\Model\ModelAwareInterface;
use PayNL\Sdk\Model\ModelAwareTrait;
use PayNL\Sdk\Serialization\JsonMapper;
use PayNL\Sdk\Service\Manager as ServiceManager;

/**
 * Class AbstractTransformer
 *
 * @package PayNL\Sdk\Transformer
 */
abstract class AbstractTransformer implements TransformerInterface, ModelAwareInterface
{
    use ModelAwareTrait;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    protected ?AbstractHydrator $hydrator = null;

    /**
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    public function getHydrator(): ?AbstractHydrator
    {
        return $this->hydrator;
    }

    public function setHydrator(AbstractHydrator $hydrator): self
    {
        $this->hydrator = $hydrator;
        return $this;
    }

    /**
     * @param string $jsonEncodedString
     *
     * @throws UnexpectedValueException
     *
     * @return mixed
     */
    protected function getDecodedInput(string $jsonEncodedString)
    {
        $transformedInput = (new JsonMapper())->decode($jsonEncodedString);

        return $this->filterNotNull($transformedInput);
    }

    /**
     * @param array $input
     *
     * @return array
     */
    protected function filterNotNull(array $input): array
    {
        $context = $this;

        return array_filter(
            array_map(static function ($item) use ($context) {
                return is_array($item) === true ? $context->filterNotNull($item) : $item;
            }, $input),
            static function ($item) {
                return $item !== '' && $item !== null;
            }
        );
    }
}
