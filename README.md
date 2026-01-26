<img src="https://www.pay.nl/uploads/1/brands/main_logo.png" width="100px" style="margin-bottom: -30px"/> <h1 style="position:relative;top:-6px;padding-left:10px;display: inline-block">PHP SDK</h1>

Provides easy integration with Pay’s powerful payment platform, enabling online and instore payments with a wide range of local and international payment methods, including iDEAL, Bancontact, Apple Pay, PayPal, Klarna, credit cards, SEPA payments, and more.

This PHP SDK makes it easy to integrate the powerful Pay. API into your integration to start transactions, retrieve payment statuses and process refunds directly from your website or platform.

Have a look at our offer:

[Country specific payment methods](https://www.pay.nl/en/payment-solutions-per-region)

[Online card payments](https://www.pay.nl/en/online-cardpayments)

[Buy Now Pay Later](https://www.pay.nl/en/buynow-paylater)

[Giftcards and vouchers](https://www.pay.nl/en/giftcard-vouchers)

[Instore payments](https://www.pay.nl/en/in-store-payments)

<hr>

## Requirements

To use this PHP SDK, you’ll need:
* A Pay. account (register here).
* You can immediately use the API in sandbox mode.
* API credentials which you will find in your Dashboard.
* PHP 8.1–8.4
* JSON extension
* CURL extension

Once your account is set up, you can immediately start using the API in sandbox mode and enable payment methods for live transactions when ready.

<hr>

## Installation
### Using Composer

The recommended way to install the Pay. PHP SDK is via Composer:
```
    composer require paynl/php-sdk
```

More info about installation [here](https://github.com/paynl/php-sdk/wiki/Install).

<hr>

## Usage
### Creating a transaction

```php
use PayNL\Sdk\Model\Request\OrderCreateRequest;

$payOrder = (new OrderCreateRequest())
    ->setServiceId($serviceId)
    ->setAmount(1.95) // Currency default in EURO.
    ->setReturnUrl('https://yourdomain/finish.php')
    ->start();

echo $payOrder->getPaymentUrl();
```

The response contains the order ID and redirect URL needed to forward the customer to the selected payment method.

See also this minimum create order [example](https://github.com/paynl/php-sdk/blob/main/samples/OrderCreateMinimal.php)

<hr>

## Documentation

For complete and up-to-date documentation, please explore our Developer Portal. Documentation is available in English.

* API endpoints and parameters {add links}
* Creating and managing transactions {add links}
* Payment methods and statuses {add links}
* Refunds and partial refunds {add links}
* Webhooks (exchange URLs) {add links}
* Testing and debugging {add links}

Detailed SDK usage examples can be found in this repository and in the official Developer Portal.

<hr>

## Examples & Recipes

The Pay. PHP SDK includes practical [examples](https://github.com/paynl/SDK-PHP/blob/main/samples) for common use cases, such as:
* Starting a payment
* Handling exchange URLs (webhooks)
* Retrieving transaction details
* Processing refunds
* Working with recurring and deferred payment methods (where applicable)

<hr>

## Upgrading

Please refer to the UPGRADING.md file for details on breaking changes and migration steps between SDK versions.

<hr>

## Contributing

Contributions are welcome. Feel free to submit issues or pull requests to help improve the Pay. PHP SDK.

<hr>

## Support

Website: [Pay.nl](https://www.pay.nl/)

Documentation: Developer.pay

E-mail: [support@pay.nl](support@pay.nl)
