<?php

declare(strict_types=1);

namespace PayNL\Sdk\Request;

use PayNL\Sdk\{Config\ProviderInterface as ConfigProviderInterface,
  Common\ManagerFactory,
  Common\DebugAwareInitializer,
};

/**
 * Class Manager
 *
 * @package PayNL\Sdk\Request
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @return array
     */
    public function __invoke(): array
    {
        return [
          'service_manager' => $this->getDependencyConfig(),
          'service_loader_options' => [
            Manager::class => [
              'service_manager' => 'requestManager',
              'config_key' => 'requests',
              'class_method' => 'getRequestConfig'
            ],
          ],
          'request' => [
            'format' => RequestInterface::FORMAT_OBJECTS,
          ],
          'domainMapping' => []
        ];
    }

    /**
     * @return array
     */
    public function getDependencyConfig(): array
    {
        return [
          'aliases' => [
            'requestManager' => Manager::class,
          ],
          'factories' => [
            Manager::class => ManagerFactory::class,
          ],
        ];
    }

    /**
     * @return array
     */
    public function getRequestConfig(): array
    {
        return [
          'aliases' => [
            'Request' => Request::class,
          ],
          'initializers' => [
            DebugAwareInitializer::class,
          ],
          'services' => array_merge(
              $this->getIsPayServicesConfig(),
              $this->getPinServicesConfig(),
              $this->getServiceServicesConfig(),
              $this->getTransactionServicesConfig(),
          ),
          'factories' => [
            Request::class => Factory::class,
          ],
        ];
    }

    /**
     * @return array
     */
    protected function getIsPayServicesConfig(): array
    {
        return [
          'IsPay' => [
            'uri' => '/ispay/ip?value=%value%',
            'method' => RequestInterface::METHOD_GET,
            'requiredParams' => [
              'value' => '[0-9\.]+',
            ],
          ],
          'GetIpAddresses' => [
            'uri' => '/ipaddresses',
            'method' => RequestInterface::METHOD_GET,
            'requiredParams' => [],
          ],
        ];
    }

    /**
     * @return array
     */
    protected function getPinServicesConfig(): array
    {
        return [
          'TerminalsGet' => [
            'uri' => '/terminals/%terminalCode%',
            'method' => RequestInterface::METHOD_GET,
            'requiredParams' => ['terminalCode' => ''],
          ],
          'TerminalsBrowse' => [],
          'ConfirmTerminalTransaction' => [
            'uri' => '/pin/%terminalTransactionId%/confirm',
            'method' => RequestInterface::METHOD_PATCH,
            'requiredParams' => ['terminalTransactionId' => 'TT(-\d{4}){3,}'],
          ],
          'GetReceipt' => [
            'uri' => '/pin/%terminalTransactionId%/receipt',
            'method' => RequestInterface::METHOD_GET,
            'requiredParams' => [
              'terminalTransactionId' => 'TT(-\d{4}){3,}',
            ],
          ],
          'GetTerminals' => [
            'uri' => '/pin/terminals',
            'method' => RequestInterface::METHOD_GET,
          ],
          'GetTerminalTransactionStatus' => [
            'uri' => '/pin/%terminalTransactionId%/status',
            'method' => RequestInterface::METHOD_GET,
            'requiredParams' => [
              'terminalTransactionId' => 'TT(-\d{4}){3,}',
            ],
          ],
          'PayTransaction' => [
            'uri' => '/pin/%terminalTransactionId%/payment',
            'method' => RequestInterface::METHOD_POST,
            'requiredParams' => [
              'terminalTransactionId' => 'TT(-\d{4}){3,}',
            ],
          ],
          'TerminalPaymentStatus' => [
            'uri' => '/api/status?hash=%hash%&timeout=%timeout%',
            'method' => RequestInterface::METHOD_GET,
            'requiredParams' => [
              'hash' => '',
              'timeout' => ''
            ],
          ],
          'TerminalCancelPayment' => [
            'uri' => '/api/cancel?hash=%hash%',
            'method' => RequestInterface::METHOD_GET,
            'requiredParams' => [
              'hash' => '',
            ],
          ],
        ];
    }


    /**
     * @return array
     */
    protected function getServiceServicesConfig(): array
    {
        return [
          'GetService' => [
            'uri' => '/services/%serviceId%',
            'method' => RequestInterface::METHOD_GET,
            'requiredParams' => [
              'serviceId' => 'SL(-\d{4}){2,}',
            ],
          ],
          'GetConfig' => [
            'uri' => '/services/config',
            'method' => RequestInterface::METHOD_GET,
            'requiredParams' => [],
            'optionalParams' => [
              'serviceId' => 'SL(-\d{4}){2,}',
            ],
          ],
        ];
    }

    /**
     * @return array
     */
    protected function getTransactionServicesConfig(): array
    {
        return [
          'OrderVoid' => [
            'uri' => '/',
            'method' => RequestInterface::METHOD_PATCH,
            'requiredParams' => [
              'transactionId' => '',
            ],
          ],
          'OrderAbort' => [
            'uri' => '/',
            'method' => RequestInterface::METHOD_PATCH,
            'requiredParams' => [
              'transactionId' => '',
            ],
          ],
          'OrderUpdate' => [
            'uri' => '/',
            'method' => RequestInterface::METHOD_PATCH,
            'requiredParams' => [
                'transactionId' => '',
            ],
          ],
          'OrderCapture' => [
            'uri' => '',
            'requiredParams' => [
              'transactionId' => '',
            ],
          ],
            'OrderCaptureLegacy' => [
                'uri' => '',
                'requiredParams' => [
                    'transactionId' => '',
                ],
            ],
          'TransactionVoid' => [
            'uri' => '',
            'requiredParams' => [
              'transactionId' => '',
            ],
          ],
          'OrderCreate' => [
            'uri' => '/orders',
            'method' => RequestInterface::METHOD_POST,
          ],
          'DeclineTransaction' => [
            'uri' => '/transactions/%transactionId%/decline',
            'method' => RequestInterface::METHOD_PATCH,
            'requiredParams' => [
              'transactionId' => 'EX(-\d{4}){3,}',
            ],
          ],
          'TransactionStatus' => [
                'uri' => '/transactions/%transactionId%/status',
                'method' => RequestInterface::METHOD_GET,
                'requiredParams' => [
                'transactionId' => '',
                ],
          ],
          'OrderStatus' => [
            'uri' => '/transactions/%transactionId%/status',
            'method' => RequestInterface::METHOD_GET,
            'requiredParams' => [
              'transactionId' => '',
            ],
          ],
          'OrderApprove' => [
            'uri' => '/',
            'method' => RequestInterface::METHOD_PATCH,
            'requiredParams' => [
              'transactionId' => '',
            ],
          ],
          'OrderDecline' => [
            'uri' => '/',
            'method' => RequestInterface::METHOD_PATCH,
            'requiredParams' => [
              'transactionId' => '',
            ],
          ],
          'TransactionRefund' => [
            'uri' => '/transactions/%transactionId%/refund',
            'method' => RequestInterface::METHOD_PATCH,
            'requiredParams' => [
              'transactionId' => '',
            ],
          ],
          'RefundTransaction' => [
            'uri' => '/transactions/%transactionId%/refund',
            'method' => RequestInterface::METHOD_PATCH,
            'requiredParams' => [
              'transactionId' => '',
            ],
          ]
        ];
    }
}
