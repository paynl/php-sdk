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
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
                'paymentMethods'        => 'paymentMethod',
                'products'              => 'product',
                'productTypes'          => 'productType',
                'merchants'             => 'merchant',
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
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getModelConfig(): array
    {
        return [
            'aliases' => [
                'address'               => 'Address',
                'amount'                => 'Amount',
                'card'                  => 'Card',
                'company'               => 'Company',
                'companyCard'           => 'CompanyCard',
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
                'ipAddresses'           => 'IpAddresses',
                'productTypes'          => 'ProductTypes',
                'productType'           => 'ProductType',
                'merchant'              => 'Merchant',
                'merchants'             => 'Merchants',
                'notification'          => 'Notification',
                'order'                 => 'Order',
                'paymentMethod'         => 'PaymentMethod',
                'paymentMethods'        => 'PaymentMethods',
                'product'               => 'Product',
                'products'              => 'Products',
                'progress'              => 'Progress',
                'receipt'               => 'Receipt',
                'refund'                => 'Refund',
                'refundTransaction'     => 'RefundTransaction',
                'refundedtransactions'  => 'RefundedTransactions',
                'refunded_transactions' => 'RefundedTransactions',
                'refundedTransactions'  => 'RefundedTransactions',
                'failedTransactions'    => 'RefundedTransactions',
                'failedtransactions'    => 'RefundedTransactions',
                'failed_transactions'   => 'RefundedTransactions',
                'statistics'            => 'Statistics',
                'status'                => 'Status',
                'terminal'              => 'Terminal',
                'terminals'             => 'Terminals',
                'terminalTransaction'   => 'TerminalTransaction',
                'terminalPaymentStatus' => 'TerminalPaymentStatus',
                'terminalCancelPayment' => 'TerminalCancelPayment',
                'transaction'           => 'Transaction',
            ],
            'invokables' => [
                'Address'               => Address::class,
                'Amount'                => Amount::class,
                'BankAccount'           => BankAccount::class,
                'Card'                  => Card::class,
                'Company'               => Company::class,
                'CompanyCard'           => CompanyCard::class,
                'Currencies'            => Currencies::class,
                'Currency'              => Currency::class,
                'Customer'              => Customer::class,
                'Countries'             => Countries::class,
                'Country'               => Country::class,
                'Document'              => Document::class,
                'Error'                 => Error::class,
                'Errors'                => Errors::class,
                'Integration'           => Integration::class,
                'Interval'              => Interval::class,
                'Issuers'               => Issuers::class,
                'Issuer'                => Issuer::class,
                'Link'                  => Link::class,
                'Links'                 => Links::class,
                'Language'              => Language::class,
                'Languages'             => Languages::class,
                'IpAddresses'           => IpAddresses::class,
                'ProductType'           => ProductType::class,
                'ProductTypes'          => ProductTypes::class,
                'Merchant'              => Merchant::class,
                'Merchants'             => Merchants::class,
                'Notification'          => Notification::class,
                'Order'                 => Order::class,
                'PaymentMethod'         => PaymentMethod::class,
                'PaymentMethods'        => PaymentMethods::class,
                'Product'               => Product::class,
                'Products'              => Products::class,
                'Progress'              => Progress::class,
                'Receipt'               => Receipt::class,
                'Refund'                => Refund::class,
                'RefundTransaction'     => RefundTransaction::class,
                'RefundedTransactions'  => RefundedTransactions::class,
                'Config'                => Config::class,
                'Stats'                 => Stats::class,
                'Status'                => Status::class,
                'Terminal'              => Terminal::class,
                'Terminals'             => Terminals::class,
                'TerminalTransaction'   => TerminalTransaction::class,
                'TerminalPaymentStatus' => TerminalPaymentStatus::class,
                'TerminalCancelPayment' => TerminalPaymentStatus::class,
                'Transaction'           => Transaction::class,
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
