<?php

declare(strict_types=1);

namespace PayNL\Sdk\Serialization;

use JsonException;
use JsonSerializable;
use PayNL\Sdk\Exception\UnexpectedValueException;
use Throwable;

final class JsonMapper
{
    public const DEFAULT_ENCODE_OPTIONS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    /**
     * @throws UnexpectedValueException
     */
    public function encode(mixed $data, bool $prettyPrint = false): string
    {
        $options = self::DEFAULT_ENCODE_OPTIONS | JSON_THROW_ON_ERROR;
        if ($prettyPrint === true) {
            $options |= JSON_PRETTY_PRINT;
        }

        try {
            return json_encode($this->normalize($data), $options);
        } catch (JsonException $jsonException) {
            throw new UnexpectedValueException('Unable to encode JSON payload', 500, $jsonException);
        }
    }

    /**
     * @throws UnexpectedValueException
     */
    public function decode(string $json): array
    {
        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $jsonException) {
            throw new UnexpectedValueException('Unable to decode the response', 500, $jsonException);
        }

        if (is_array($data) === false) {
            throw new UnexpectedValueException('Unable to decode the response', 500);
        }

        return $data;
    }

    /**
     * @return mixed
     */
    public function normalize(mixed $data): mixed
    {
        if ($data instanceof JsonSerializable) {
            return $this->normalize($data->jsonSerialize());
        }

        if (is_array($data) === true) {
            $normalized = [];
            foreach ($data as $key => $value) {
                $value = $this->normalize($value);
                if ($value === null || $value === '') {
                    continue;
                }

                if (is_array($value) === true && count($value) === 0) {
                    continue;
                }

                $normalized[$key] = $value;
            }

            return $normalized;
        }

        if (is_object($data) === true && method_exists($data, '__toString')) {
            return (string)$data;
        }

        if (is_object($data) === true) {
            return $this->normalize($this->extractObject($data));
        }

        return $data;
    }

    private function extractObject(object $object): array
    {
        $data = [];
        foreach (get_class_methods($object) as $method) {
            if ($method === 'getIterator') {
                continue;
            }

            if (str_starts_with($method, 'get') === true) {
                $key = lcfirst(substr($method, 3));
            } elseif (str_starts_with($method, 'is') === true) {
                $key = lcfirst(substr($method, 2));
            } else {
                continue;
            }

            try {
                $data[$key] = $object->$method();
            } catch (Throwable) {
                continue;
            }
        }

        return $data;
    }
}
