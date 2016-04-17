# Ssdp

[![Latest Version on Packagist](https://img.shields.io/packagist/v/gravitymedia/ssdp.svg)](https://packagist.org/packages/gravitymedia/ssdp)
[![Software License](https://img.shields.io/packagist/l/gravitymedia/ssdp.svg)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/GravityMedia/Ssdp.svg)](https://travis-ci.org/GravityMedia/Ssdp)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/GravityMedia/Ssdp.svg)](https://scrutinizer-ci.com/g/GravityMedia/Ssdp/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/GravityMedia/Ssdp.svg)](https://scrutinizer-ci.com/g/GravityMedia/Ssdp)
[![Total Downloads](https://img.shields.io/packagist/dt/gravitymedia/ssdp.svg)](https://packagist.org/packages/gravitymedia/ssdp)
[![Dependency Status](https://img.shields.io/versioneye/d/php/gravitymedia:ssdp.svg)](https://www.versioneye.com/user/projects/54a6c3ea27b014d85a000192)

Simple Service Discovery Protocol (SSDP) library for PHP.

## Requirements

This library has the following requirements:

- PHP 5.6+

## Installation

Install Composer in your project:

```bash
$ curl -s https://getcomposer.org/installer | php
```

Add the package to your `composer.json` and install it via Composer:

```bash
$ php composer.phar require gravitymedia/ssdp
```

## Usage

```php
// Initialize autoloader
require 'vendor/autoload.php';

// Import classes
use GravityMedia\Ssdp\Client;
use GravityMedia\Ssdp\Options\DiscoverOptions;
use React\EventLoop\Factory as LoopFactory;

// Create event dispatcher
$loop = LoopFactory::create();
$client = new Client($loop);

$options = new DiscoverOptions();

$client->discover($options)->then(
    function () {
        print 'Discovery completed.' . PHP_EOL;
    },
    function ($reason) {
        print 'An error occurred: ' . $reason . PHP_EOL;
    },
    function ($progress) {
        print 'Device found:' . PHP_EOL;
        var_dump($progress);
    }
);

$loop->run();
```

## Testing

Clone this repository, install Composer and all dependencies:

``` bash
$ php composer.phar install
```

Run the test suite:

``` bash
$ php composer.phar test
```

## Generating documentation

Clone this repository, install Composer and all dependencies:

``` bash
$ php composer.phar install
```

Generate the documentation to the `build/docs` directory:

``` bash
$ php composer.phar doc
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Daniel Schröder](https://github.com/pCoLaSD)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
