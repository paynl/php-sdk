<?php

declare(strict_types=1);

namespace PayNL\Sdk\Mapper;

use PayNL\Sdk\Config\ProviderInterface;
use PayNL\Sdk\Common\ManagerFactory;

/**
 * Class ConfigProvider
 *
 * @package PayNL\Sdk\Mapper
 */
class ConfigProvider implements ProviderInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(): array
    {
        return [
            'service_manager' => $this->getDependencyConfig(),
            'service_loader_options' => [
                Manager::class => [
                    'service_manager' => 'mapperManager',
                    'config_key'      => 'mappers',
                    'class_method'    => 'getMapperConfig',
                ],
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getDependencyConfig(): array
    {
        return [
            'aliases' => [
                'mapperManager' => Manager::class,
            ],
            'factories' => [
                Manager::class => ManagerFactory::class,
            ],
        ];
    }

    /**
     * Service manager definition for the models of this component
     *
     * @return array
     */
    public function getMapperConfig(): array
    {
        return [
            'aliases' => [
                'RequestModelMapper' => RequestModelMapper::class,
            ],
            'factories' => [
                RequestModelMapper::class => Factory::class,
            ],
            'mapping' => $this->getMap(),
        ];
    }

    /**
     * Mapping for setting the correct model for the corresponding request. If a
     * request is not in the list the text for the corresponding status code will
     * be shown.
     *
     * @return array
     */
    public function getMap(): array
    {
        return [
            'RequestModelMapper' => [
                'GetTerminals'                  => 'Terminals',
                'GetTerminalTransactionStatus'  => 'TerminalTransaction',
                'PayTransaction'                => 'TerminalTransaction',
                'GetConfig'                     => 'ServiceGetConfigResponse',
                'GetIpAddresses'                => 'IpAddresses',
                'TerminalPaymentStatus'         => 'TerminalPaymentStatus',
                'TerminalCancelPayment'         => 'TerminalPaymentStatus',
                'DeclineTransaction'            => 'Transaction',
                'TransactionRefund'             => 'TransactionRefundResponse',
                'TransactionStatus'             => 'PayOrder',
                'OrderCreate'                   => 'PayOrder',
                'OrderCapture'                  => 'PayOrder',
                'OrderCaptureLegacy'            => 'PayOrder',
                'OrderVoid'                     => 'PayOrder',
                'OrderAbort'                    => 'PayOrder',
                'OrderStatus'                   => 'PayOrder',
                'OrderUpdate'                   => 'PayOrder',
                'OrderApprove'                  => 'PayOrder',
                'OrderDecline'                  => 'PayOrder',
                'TerminalsGet'                   => 'Terminal',
                'TerminalsBrowse'                => 'Terminals'
            ],
        ];
    }
}
