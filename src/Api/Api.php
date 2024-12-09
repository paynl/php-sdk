<?php

declare(strict_types=1);

namespace PayNL\Sdk\Api;

use PayNL\Sdk\{
    AuthAdapter\AdapterInterface as AuthAdapterInterface,
    Common\DebugAwareInterface,
    Common\DebugAwareTrait,
    Common\OptionsAwareInterface,
    Common\OptionsAwareTrait,
    Response\Response,
    Request\AbstractRequest,
    Request\RequestInterface,
    Response\ResponseInterface
};
use GuzzleHttp\Client;

/**
 * Class Api
 *
 * @package PayNL\Sdk\Api
 */
class Api implements OptionsAwareInterface, DebugAwareInterface
{
    use DebugAwareTrait;
    use OptionsAwareTrait;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var AuthAdapterInterface
     */
    protected $authAdapter;

    /**
     * Api constructor.
     *
     * @param AuthAdapterInterface $authenticationAdapter
     * @param Client $client
     * @param array $options
     */
    public function __construct(AuthAdapterInterface $authenticationAdapter, Client $client, array $options = [])
    {
        $this->setAuthAdapter($authenticationAdapter)
            ->setClient($client)
            ->setOptions($options)
        ;
    }

    /**
     * @param Client $client
     *
     * @return Api
     */
    protected function setClient(Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return AuthAdapterInterface
     */
    public function getAuthAdapter(): AuthAdapterInterface
    {
        return $this->authAdapter;
    }

    /**
     * @param AuthAdapterInterface $adapter
     *
     * @return Api
     */
    protected function setAuthAdapter(AuthAdapterInterface $adapter): self
    {
        $this->authAdapter = $adapter;
        return $this;
    }

    /**
     * Handle the actual request by executing it and return the populated response object
     *
     * @param RequestInterface $request
     * @param Response $response
     *
     * @return Response
     */
    public function doHandle(RequestInterface $request, Response $response): Response
    {
        $format = $request->getFormat();
        $request->applyClient($this->getClient());

        # Apply the correct headers based on the formats set on request and response object
        # and also add the authentication header which is based on the authentication adapter
        $contentTypeHeader = 'application/json';
        if (ResponseInterface::FORMAT_XML === $format) {
            $contentTypeHeader = 'application/xml';
        }

        $format = $response->getFormat();
        $acceptHeader = 'application/json';
        if (RequestInterface::FORMAT_XML === $format) {
            $acceptHeader = 'application/xml';
        }

        if ($request instanceof AbstractRequest) {
            $request->setHeader(RequestInterface::HEADER_ACCEPT, $acceptHeader)
                ->setHeader(RequestInterface::HEADER_CONTENT_TYPE, $contentTypeHeader)
                ->setHeader(RequestInterface::HEADER_AUTHORIZATION, $this->getAuthAdapter()->getHeaderString());
        }

        $request->execute($response);

        return $response;
    }
}
