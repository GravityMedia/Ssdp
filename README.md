# Ssdp

[![Latest Version on Packagist](https://img.shields.io/packagist/v/gravitymedia/ssdp.svg)](https://packagist.org/packages/gravitymedia/ssdp)
[![Software License](https://img.shields.io/packagist/l/gravitymedia/ssdp.svg)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/GravityMedia/Ssdp.svg)](https://travis-ci.org/GravityMedia/Ssdp)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/GravityMedia/Ssdp.svg)](https://scrutinizer-ci.com/g/GravityMedia/Ssdp/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/GravityMedia/Ssdp.svg)](https://scrutinizer-ci.com/g/GravityMedia/Ssdp)
[![Total Downloads](https://img.shields.io/packagist/dt/gravitymedia/ssdp.svg)](https://packagist.org/packages/gravitymedia/ssdp)
[![Dependency Status](https://img.shields.io/versioneye/d/php/gravitymedia:ssdp.svg)](https://www.versioneye.com/user/projects/54a6c39d27b014005400004b)

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
use GravityMedia\Ssdp\SsdpEvent;
use GravityMedia\Ssdp\SsdpMessenger;
use Symfony\Component\EventDispatcher\EventDispatcher;

// Create event dispatcher
$eventDispatcher = new EventDispatcher();

// Create SSDP messenger
$ssdpMessenger = new SsdpMessenger($eventDispatcher);

// Add discovery listener
$eventDispatcher->addListener(
    SsdpEvent::DISCOVER,
    function (SsdpEvent $event) {
        // Dump response
        var_dump($event->getResponse());
    }
);

// Discover devices and services
$ssdpMessenger->discover();
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
