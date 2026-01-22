<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model;

/**
 * Interface ModelAwareInterface
 *
 * @package PayNL\Sdk\Model
 */
interface ModelAwareInterface
{
    /**
     * @return ModelInterface|null
     */
    public function getModel(): ?ModelInterface;

    /**
     * @param ModelInterface $model
     * @return mixed
     */
    public function setModel(ModelInterface $model);
}
