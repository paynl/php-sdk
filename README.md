<img  src="https://www.pay.nl/uploads/1/brands/main_logo.png" width="100px" style="margin-bottom: -30px"/> <h1 style="position:relative;top:-6px;padding-left:10px;display: inline-block">PHP SDK</h1>

### Requirements
Minimum PHP: 7.4  
Tested up to PHP: 8.3  
CURL-extension must be installed  
JSON-extension must be installed  
<br>

### Installation
<b>Install the SDK with composer via:</b>
```bash
composer require paynl/php-sdk
```  
<br>

<b>Or install the SDK from a zipfile:</b><br>  
Download the zipfile that is included in the release itself.  
Then unzip the contents of the file and upload it to your server.  
In your project, require the file vendor/autoload.php.  
<br>

### Configuration
When the installation is completed, you have two configuration options:  
- Set the global config: Do this in /config/config.global.php.
- Or set the config when you want to connect to an endpoint. Like this:
```php
use PayNL\Sdk\Config\Config;

$config = new Config();
$config->setUsername($yourUsername); # Your tokencode
$config->setPassword($yourPassword); # Your API token
$myRequest->setConfig($config);
```

Notice: It is also possible to authenticate with SL-code as username and secret as password.

### Samples
To get you started we suggest you take a look at the samples folder.
For every endpoint is a sample available.

For further details and instructions, please take a look at SDK's [wiki page](https://github.com/paynl/SDK-PHP/wiki).

 