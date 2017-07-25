## PsyshBundle
_Work in progress.._

[![Latest Stable Version](https://poser.pugx.org/alexmasterov/psysh-bundle/v/stable)](https://packagist.org/packages/alexmasterov/psysh-bundle)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/AlexMasterov/psysh-bundle/master/LICENSE)
[![Build Status](https://travis-ci.org/AlexMasterov/psysh-bundle.svg)](https://travis-ci.org/AlexMasterov/psysh-bundle)
[![Code Quality](https://scrutinizer-ci.com/g/AlexMasterov/psysh-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/AlexMasterov/psysh-bundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/AlexMasterov/psysh-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/AlexMasterov/psysh-bundle/?branch=master)

The bundle fully integrates the [PsySH](http://psysh.org/) into the Symfony framework.

## Installation

The suggested installation method is via [composer](https://getcomposer.org/):

```sh
$ composer require alexmasterov/psysh-bundle
```

Add the bundle in your application kernel:
```php
// AppKernel.php
public function registerBundles()
{
    return [
        // ...
        new AlexMasterov\PsyshBundle\PsyshBundle(),
    ];
}
```

## Usage
For PsySH run the following command:
```sh
$ php bin/console psysh:shell
```
