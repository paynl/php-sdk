<?php

declare(strict_types=1);

namespace PayNL\Sdk\Packages\Laminas\Stdlib;

interface DispatchableInterface
{
    /**
     * Dispatch a request
     *
     * @return Response|mixed
     */
    public function dispatch(RequestInterface $request, ?ResponseInterface $response = null);
}
