<?php

declare(strict_types=1);

namespace PayNL\Sdk\Request;

use Exception;
use Throwable;
use PayNL\Sdk\Config\Config;
use PayNL\Sdk\Application\Application;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Util\Text;

/**
 * Class RequestData
 *
 * @package PayNL\Sdk\Request
 */
abstract class RequestData implements RequestDataInterface
{
    protected Application $application;
    protected string $mapperName = '';
    protected string $uri = '';
    protected string $methodType = 'GET';
    protected ?Config $config;

    /**
     * @param string $mapperName Internal name of the call to make
     * @param string $uri Path for API
     * @param string $requestMethod Should be for example RequestInterface::METHOD_POST, RequestInterface::METHOD_GET, etc.
     */
    public function __construct(string $mapperName, string $uri, string $requestMethod = 'POST')
    {
        $this->mapperName = $mapperName;
        $this->methodType = $requestMethod;
        $this->uri = $uri;
        $this->config = new Config();
    }

    /**
     * @param Config $config
     * @return $this
     */
    public function setConfig(Config $config): self
    {
        $this->config = (new Config(require __DIR__ . '/../../config/config.global.php'));
        $this->config->merge($config);
        return $this;
    }

    /**
     * @param Application $application
     * @return void
     */
    public function setApplication(Application $application): void
    {
        $this->application = $application;
    }


    /**
     * @return Config|null
     * @throws PayException
     */
    private function getConfig(): Config
    {
        if (empty($this->config)) {
            $config = (new Config(require __DIR__ . '/../../config/config.global.php'));
        } else {
            $config = $this->config;
        }

        if (!empty($config->getFailoverUrl())) {
            $config->setCore($config->getFailoverUrl());
        }

        if ($config->isEmpty()) {
            throw new PayException('Please check your config', 0, 0);
        }

        return $config;
    }

    /**
     * @return mixed
     * @throws PayException
     */
    public function start()
    {
        $config = $this->getConfig();

        try {
            if (empty($this->application)) {
                $this->application = Application::init($config);
            }

            $response = $this->application->request($this)->run();
        } catch (\Throwable $e) {
            throw (new PayException('Could not initiate API call:' . $e->getMessage(), 0, 0))
                ->setFriendlyMessage(Text::getFriendlyMessage($e->getMessage()));
        }

        if ($response->hasErrors()) {
            $jsonData = json_decode($response->getRawBody(), true);
            $jsonError = json_last_error();

            if (empty($jsonError) && !empty($jsonData)) {
                $code = $jsonData['violations'][0]['code'] ?? 'PAY-0';
                $detail = $jsonData['detail'] ?? '';
                $errorMessage = empty($detail) ? ($jsonData['title'] ?? '') : $detail;
                $errorMessage = (empty($errorMessage) && !empty($jsonData['error'])) ? $jsonData['error'] : $errorMessage;
                if (empty($errorMessage) && isset($jsonData['errors']['transactionId']['message'])) {
                    $errorMessage = $jsonData['errors']['transactionId']['message'];
                }
                $detail = empty($detail) ? $errorMessage : $detail;
                throw (new PayException($code . ' - ' . $detail, (int)substr($code, 4), $response->getStatusCode()))->setFriendlyMessage(Text::getFriendlyMessage($errorMessage));
            } else {
                throw (new PayException($response->getErrors(), 0, $response->getStatusCode()))->setFriendlyMessage(Text::getFriendlyMessage($response->getErrors()));
            }
        } else {
            $responseBody = $response->getBody();
            if (gettype($responseBody) != 'object') {
                throw new PayException('Unexpected result, could not transform.', 0, $response->getStatusCode());
            }
            return $responseBody;
        }
    }

    /**
     * @return array
     */
    abstract public function getPathParameters(): array;

    /**
     * @return array
     */
    abstract public function getBodyParameters(): array;

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $this->methodType;
    }

    /**
     * @return string
     */
    public function getMethodName(): string
    {
        return $this->mapperName;
    }
}
