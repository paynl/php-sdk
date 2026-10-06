<?php

/** @noinspection ALL */

declare(strict_types=1);

namespace PayNL\Sdk\Request;

use PayNL\Sdk\{
    Common\DebugAwareInterface,
    Common\DebugAwareTrait,
    Common\FormatAwareTrait,
    Common\OptionsAwareInterface,
    Common\OptionsAwareTrait,
    Exception\EmptyRequiredMemberException,
    Exception\ExceptionInterface,
    Exception\MissingParamException,
    Exception\MissingRequiredMemberException,
    Exception\RuntimeException,
    Model\ModelInterface,
    Response\Response,
    Exception\InvalidArgumentException,
    Filter\FilterInterface,
    Serialization\JsonMapper,
    Validator\ValidatorManagerAwareInterface,
    Validator\ValidatorManagerAwareTrait
};
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request as Psr7Request;

/**
 * Class AbstractRequest
 *
 * @package PayNL\Sdk\Request
 *
 */
abstract class AbstractRequest implements
    RequestInterface,
    DebugAwareInterface,
    OptionsAwareInterface,
    ValidatorManagerAwareInterface
{
    use DebugAwareTrait;
    use OptionsAwareTrait;
    use ValidatorManagerAwareTrait;
    use FormatAwareTrait;

    /*
     * Tag name declaration for XML request string
     */
    public const XML_ROOT_NODE_NAME = 'request';

    /**
     * @var string
     */
    protected $uri;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $requiredParams = [];
    protected $optionalParams = [];

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var null|string
     */
    protected $body;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $clientBaseUri = '';

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var array
     */
    protected $params = [];

    /**
     * AbstractRequest constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);

        if (true === $this->hasOption('format') && true === is_string($this->getOption('format'))) {
            $this->setFormat($this->getOption('format'));
        }

        if (true === $this->hasOption('headers') && true === is_array($this->getOption('headers'))) {
            $this->setHeaders($this->getOption('headers'));
        }

        $this->init();
    }

    /**
     * Method to execute custom code just after the request construction
     *
     * @return void
     */
    public function init(): void
    {
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param string|integer $name
     *
     * @return mixed|null
     */
    public function getParam($name)
    {
        if (false === $this->hasParam($name)) {
            return null;
        }
        return $this->params[$name];
    }

    /**
     * @param string|integer $name
     *
     * @return boolean
     */
    public function hasParam($name): bool
    {
        return array_key_exists($name, $this->params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function setParams(array $params): self
    {
        $this->params = $params;

        $queryParams = [];
        $uri = $this->getUri();

        foreach ($params as $key => $value) {
            $placeholder = "%{$key}%";

            if (strpos($uri, $placeholder) !== false) {
                $uri = str_replace($placeholder, $value, $uri);
            } else {
                $queryParams[$key] = $value;
            }
        }

        if (!empty($queryParams)) {
            $uri .= '?' . http_build_query($queryParams);
        }

        $this->setUri($uri);

        return $this;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     *
     * @return AbstractRequest
     */
    public function setUri(string $uri): self
    {
        $this->uri = '/' . trim($uri, '/');
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     *
     * @return AbstractRequest
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return array
     */
    public function getRequiredParams(): array
    {
        return $this->requiredParams;
    }

    /**
     * @return array
     */
    public function getOptionalParams(): array
    {
        return $this->optionalParams;
    }


    /**
     * @param array $requiredParams
     *
     * @return AbstractRequest
     */
    public function setRequiredParams(array $requiredParams): self
    {
        $this->requiredParams = $requiredParams;
        return $this;
    }

    /**
     * @param array $optionalParams
     * @return $this
     */
    public function setOptionalParams(array $optionalParams): self
    {
        $this->optionalParams = $optionalParams;
        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return AbstractRequest
     */
    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * @param array $headers
     *
     * @return AbstractRequest
     */
    public function setHeaders(array $headers): self
    {
        foreach ($headers as $name => $header) {
            $this->setHeader($name, $header);
        }
        return $this;
    }

    /**
     * @param string $name
     *
     * @return string|null
     */
    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        if (false === is_string($this->body) && null !== $this->body) {
            /** @var ModelInterface $body */
            $body = $this->body;
            // validate the given body (model) for the required members
            $this->validateBody($body);
            return $this->encodeBody($body);
        }

        return (string)$this->body;
    }

    /**
     * Automatically converts the given body to a string based
     * on the set format
     *
     * @param mixed $body
     *
     * @return AbstractRequest
     */
    public function setBody(mixed $body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @param Client $client
     * @param string $baseUri
     * @return $this
     */
    public function applyClient(Client $client, string $baseUri = ''): self
    {
        $this->client = $client;
        $this->clientBaseUri = $baseUri;
        return $this;
    }

    /**
     * @return Client
     */
    public function getClient(): ?Client
    {
        return $this->client;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param string $name
     *
     * @return FilterInterface|null
     */
    public function getFilter(string $name): ?FilterInterface
    {
        return $this->filters[$name] ?? null;
    }

    /**
     * @param array $filters
     *
     * @throws InvalidArgumentException
     *
     * @return AbstractRequest
     */
    public function setFilters(array $filters): self
    {
        // reset the filters
        $this->filters = [];

        foreach ($filters as $filter) {
            if (false === $filter instanceof FilterInterface) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Supplied filter is not of type %s',
                        FilterInterface::class
                    )
                );
            }
            $this->addFilter($filter);
        }
        return $this;
    }

    /**
     * @param FilterInterface $filter
     *
     * @return AbstractRequest
     */
    public function addFilter(FilterInterface $filter): self
    {
        $this->filters[$filter->getName()] = $filter;
        return $this;
    }

    /**
     * Automatically adds a Content-Type header
     *
     * @param mixed $body
     *
     * @return string
     */
    private function encodeBody(mixed $body): string
    {
        if (true === $this->isFormat(static::FORMAT_XML)) {
            throw new RuntimeException('XML request serialization is not supported in SDK 2.0', 500);
        }

        $this->setHeader(static::HEADER_CONTENT_TYPE, 'application/json');

        return (new JsonMapper())->encode($body, $this->isDebug());
    }

    /**
     * @param Response $response
     *
     * @throws RuntimeException When no HTTP client is set.
     *
     * @return void
     */
    public function execute(Response $response): void
    {
        $uri = trim($this->getUri(), '/');
        $filters = $this->getFilters();

        $uri = trim($uri . '?' . implode('&', $filters), '?');
        $overrideUrl = $this->getOption('url');
        $baseUri = is_string($overrideUrl) && $overrideUrl !== ''
            ? $overrideUrl
            : (string)$this->clientBaseUri;

        $this->dumpPreStringAdvanced($this->getBody(), 'Request body', 400);

        $requestBody = '';
        $curlRequest = '';
        $rawBody = '';
        $body = '';
        $statusCode = 500;

        try {
            $guzzleClient = $this->getClient();
            if (false === ($guzzleClient instanceof Client)) {
                throw new RuntimeException('No HTTP client found', 500);
            }

            $requestBody = $this->getBody();
            $method = strtoupper($this->getMethod());
            $requestUri = $uri;
            if (is_string($overrideUrl) && $overrideUrl !== '') {
                $requestUri = rtrim($overrideUrl, '/') . '/' . $uri;
            }

            $guzzleRequest = new Psr7Request($method, $requestUri, $this->getHeaders(), $requestBody);

            $curlRequest = 'curl -X ' . $method . ' ' . rtrim((string)$baseUri, '/') . '/' . $uri;
            foreach ($this->getHeaders() as $headerfield => $headervalue) {
                $curlRequest .= ' -H "' . $headerfield . ': ' . $headervalue . '"';
            }

            $curlRequest .= empty($requestBody) ? '' : ' -d \'' . $requestBody . '\'';

            $this->dumpPreString($curlRequest, 'Curl request');
            $this->dumpPreString(rtrim((string)$baseUri, '/') . '/' . $guzzleRequest->getUri(), 'Requested URL');
            $this->dumpPreString(implode(PHP_EOL, array_map(static function ($item, $key) {
                return "{$key}: {$item}";
            }, $this->getHeaders(), array_keys($this->getHeaders()))), 'Headers');

            $guzzleResponse = $guzzleClient->send($guzzleRequest, ['http_errors' => false]);
            $rawBody = $guzzleResponse->getBody()->getContents();
            $statusCode = $guzzleResponse->getStatusCode();
            $body = $rawBody;

            if ($statusCode >= 400 && $rawBody !== '') {
                $body = $this->getErrorsString($response->getFormat(), $statusCode, $rawBody);
            }
        } catch (GuzzleException | ExceptionInterface $e) {
            $statusCode = $e->getCode() ?: 500;
            $rawBody = 'Error: ' . $e->getMessage() . ' (' . $statusCode . ')';
            $body = $this->getErrorsString($response->getFormat(), (int)$statusCode, $rawBody);
        }

        if (function_exists('displayPayRequest')) {
            displayPayRequest(rtrim((string)$baseUri, '/') . '/' . $uri, $requestBody, $rawBody, $curlRequest);
        }

        $response->setStatusCode($statusCode)->setRawBody($rawBody)->setBody($body);
    }

    /**
     * @param string  $responseFormat
     * @param integer $statusCode
     * @param string  $rawBody
     *
     * @return string
     */
    private function getErrorsString(string $responseFormat, int $statusCode, string $rawBody): string
    {
        if (static::FORMAT_XML === $responseFormat) {
            throw new RuntimeException('XML response serialization is not supported in SDK 2.0', 500);
        }

        $errors = [
            'errors' => [
                'general' => [
                    'context' => 'unknown',
                    'code'    => $statusCode,
                    'message' => $rawBody,
                ]
            ]
        ];

        $errors['errors'] = $this->flattenErrors($errors['errors']);

        return (new JsonMapper())->encode($errors, $this->isDebug());
    }

    /**
     * @param array  $errors
     * @param string $context
     *
     * @return array
     */
    protected function flattenErrors(array $errors, string $context = ''): array
    {
        if (true === array_key_exists('code', $errors) && true === array_key_exists('message', $errors)) {
            $errors['context'] = $context;
            return [$context => $errors];
        }

        $return = [];
        foreach ($errors as $key => $value) {
            $return[$key] = $value;
            if (true === is_array($value)) {
                $return = $this->flattenErrors($value, ltrim($context . ".{$key}", '.'));
            }
        }
        return $return;
    }

    /**
     * Validates the given body, when it contains a ModelInterface object, if it contains all
     *  the required properties
     *
     * @param mixed $body
     *
     * @throws RuntimeException When the body is an object and it's invalid.
     *
     * @return void
     */
    protected function validateBody(mixed $body): void
    {
        if (true === is_string($body)) {
            return;
        }

        $validator = $this->getValidatorManager()->getValidatorByRequest($this);

        if (true === $validator->isValid($body)) {
            return;
        }

        // create exception stack
        $counter = 0;
        $prev = null;
        foreach ($validator->getMessages() as $type => $message) {
            $exceptionClass = MissingRequiredMemberException::class;
            if (true === in_array($type, [$validator::MSG_EMPTY_MEMBER, $validator::MSG_EMPTY_MEMBERS], true)) {
                $exceptionClass = EmptyRequiredMemberException::class;
            }
            $e = new $exceptionClass($message, 500, ($counter++ !== 0 ? $prev : null));
            $prev = $e;
        }

        throw new RuntimeException(
            sprintf(
                'Object "%s" is not valid',
                __CLASS__
            ),
            500,
            $prev
        );
    }
}
