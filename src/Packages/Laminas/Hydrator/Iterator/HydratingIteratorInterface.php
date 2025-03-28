<?php

declare(strict_types=1);

namespace PayNL\Sdk\Packages\Laminas\Hydrator\Iterator;

use Iterator;
use PayNL\Sdk\Packages\Laminas\Hydrator\HydratorInterface;

interface HydratingIteratorInterface extends Iterator
{
    /**
     * This sets the prototype to hydrate.
     *
     * This prototype can be the name of the class or the object itself;
     * iteration will clone the object.
     *
     * @param string|object $prototype
     */
    public function setPrototype($prototype): void;

    /**
     * Sets the hydrator to use during iteration.
     */
    public function setHydrator(HydratorInterface $hydrator): void;
}
