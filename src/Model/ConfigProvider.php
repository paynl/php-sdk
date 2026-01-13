<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model;

use PayNL\Sdk\{
    Config\ProviderInterface as ConfigProviderInterface,
    Common\ManagerFactory
};

/**
 * Class ConfigProvider
 *
 * @package PayNL\Sdk\Model
 */
class ConfigProvider implements ConfigProviderInterface
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
                    'service_manager' => 'modelManager',
                    'config_key'      => 'models',
                    'class_method'    => 'getModelConfig'
                ],
            ],
            'hydrator_collection_map' => [
                // CollectionEntity(Alias) => EntryEntity(Alias)
                'contactMethods'        => 'contactMethod',
                'currencies'            => 'currency',
                'errors'                => 'error',
                'issuers'               => 'issuer',
                'links'                 => 'link',
                'products'              => 'product',
                'countries'             => 'country',
                'languages'             => 'language',
                'checkoutoptions'       => 'checkoutoption',
                'methods'               => 'method',
                'terminals'             => 'terminal',
                'refundedTransactions'  => 'refundTransaction'
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
                'modelManager' => Manager::class,
            ],
            'factories' => [
                Manager::class => ManagerFactory::class,
            ],
        ];
    }

    /**
     * @return array
     */
    public function getModelConfig(): array
    {
        return [
            'aliases' => [
                'address'               => 'Address',
                'amount'                => 'Amount',
                'card'                  => 'Card',
                'company'               => 'Company',
                'currencies'            => 'Currencies',
                'currency'              => 'Currency',
                'customer'              => 'Customer',
                'countries'             => 'Countries',
                'method'                => 'Method',
                'methods'               => 'Methods',
                'checkoutoption'        => 'CheckoutOption',
                'checkoutoptions'       => 'CheckoutOptions',
                'country'               => 'Country',
                'error'                 => 'Error',
                'errors'                => 'Errors',
                'integration'           => 'Integration',
                'interval'              => 'Interval',
                'issuers'               => 'Issuers',
                'issuer'                => 'Issuer',
                'link'                  => 'Link',
                'links'                 => 'Links',
                'language'              => 'Language',
                'languages'             => 'Languages',
                'order'                 => 'Order',
                'product'               => 'Product',
                'products'              => 'Products',
                'statistics'            => 'Statistics',
                'status'                => 'Status',
                'terminal'              => 'Terminal',
                'terminals'             => 'Terminals',
            ],
            'invokables' => [
                'Address'               => Address::class,
                'Amount'                => Amount::class,
                'Company'               => Company::class,
                'Customer'              => Customer::class,
                'Error'                 => Error::class,
                'Errors'                => Errors::class,
                'Integration'           => Integration::class,
                'Link'                  => Link::class,
                'Links'                 => Links::class,
                'Order'                 => Order::class,
                'Product'               => Product::class,
                'Products'              => Products::class,
                'Stats'                 => Stats::class,
                'Terminal'              => Terminal::class,
                'Terminals'             => Terminals::class,
                'TransactionRefundResponse'  => Response\TransactionRefundResponse::class,
                'TransactionStatusResponse'  => Response\TransactionStatusResponse::class,
                'ServiceGetConfigResponse'   => Response\ServiceGetConfigResponse::class,
                'PayOrder'                   => Pay\PayOrder::class,
                'CheckoutOptions'       => CheckoutOptions::class,
                'CheckoutOption'        => CheckoutOption::class,
                'Method'                => Method::class,
                'Methods'               => Methods::class,
            ],
        ];
    }
}
