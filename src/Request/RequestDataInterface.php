<?php

declare(strict_types=1);

namespace PayNL\Sdk\Request;

/**
 * Class RequestDataInterface
 *
 * @package PayNL\Sdk\Request
 */
interface RequestDataInterface
{

    /**
     * @return string
     */
    public function getUri(): string;

    /**
     * @return string
     */
    public function getRequestMethod(): string;

    /**
     * @return string
     */
    public function getMethodName(): string;

}
