<?php

declare(strict_types=1);

namespace PayNL\Sdk\Api;

use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use PayNL\Sdk\{
    AuthAdapter\AdapterInterface as AuthAdapterInterface,
    Config\Config,
    Exception\ServiceNotFoundException,
    Exception\InvalidArgumentException,
    Common\FactoryInterface,
    Service\Manager as ServiceManager
};

/**
 * Class Factory
 *
 * @package PayNL\Sdk\Api
 */
class Factory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return Api|Service
     */
    public function __invoke(ContainerInterface $container, string $requestedName, ?array $options = null)
    {
        switch ($requestedName) {
            case Api::class:
                /** @var Config $options */
                $options = $container->get('config')->merge(new Config($options ?? []));

                $apiUrl = rtrim($options->get('api')->get('url', ''), '/');
                $filteredApiUrl = filter_var(
                    $apiUrl,
                    FILTER_VALIDATE_URL
                );

                if (false === $filteredApiUrl || 'http' === parse_url($filteredApiUrl, PHP_URL_SCHEME)) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'Invalid API URL "%s" given, make sure you use the https protocol and define a correct endpoint',
                            $apiUrl
                        )
                    );
                }

                /** @var AuthAdapterInterface $authAdapter */
                $authAdapter = $container->get('authAdapterManager')->get($options->get('authentication')->get('type', 'basic'));
                $authAdapter->setUsername($options->get('authentication')->get('username', ''))
                    ->setPassword($options->get('authentication')->get('password', ''))
                ;

                $version = $options->get('api')->get('version');
                $pathVersion = empty($version) ? '' : '/v' . $version;

                $guzzleClient = new Client([
                    'base_uri' => $filteredApiUrl . "{$pathVersion}/",
                ]);

                return new Api($authAdapter, $guzzleClient, $options->toArray());
            case Service::class:
                /** @var ServiceManager $serviceManager */
                $serviceManager = $container;
                return new Service($container->get('Api'), $serviceManager);
            default:
                throw new ServiceNotFoundException(
                    sprintf(
                        'Cannot find service for "%s"',
                        $requestedName
                    )
                );
        }
    }
}
