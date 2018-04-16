## PsyshBundle

[![Latest Stable Version](https://poser.pugx.org/alexmasterov/psysh-bundle/v/stable)](https://packagist.org/packages/alexmasterov/psysh-bundle)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/AlexMasterov/psysh-bundle/master/LICENSE)
[![Build Status](https://travis-ci.org/AlexMasterov/psysh-bundle.svg)](https://travis-ci.org/AlexMasterov/psysh-bundle)
[![Code Coverage](https://scrutinizer-ci.com/g/AlexMasterov/psysh-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/AlexMasterov/psysh-bundle/?branch=master)
[![Code Quality](https://scrutinizer-ci.com/g/AlexMasterov/psysh-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/AlexMasterov/psysh-bundle/?branch=master)

The bundle fully integrates the [PsySH](http://psysh.org/) into the Symfony framework.

## Installation

The suggested installation method is via [composer](https://getcomposer.org/):

```sh
$ composer require alexmasterov/psysh-bundle
```

Add the bundle in your config:
```php
// config/bundles.php
return [
    // ...
    AlexMasterov\PsyshBundle\PsyshBundle::class => ['dev' => true],
];
```

## Usage
For PsySH run the following command:
```sh
$ php bin/console psysh:shell
```

## Useful cases
```yml
services:
    Controller\:
        resource: '../src/Controller'
        tags: ['psysh.variable']

    Service:
        tags:
            - { name: psysh.variable, var: mail }
```
```yml
psysh:
  variables:
    - @service
    - { db: PDO }

```
```sh
$ php bin/console psysh:shell
>>> ls
Variables: $someController, $mail, $someService, $db
```
_WIP_...

## Configuration
Some common options. For a more detailed list, see [wiki](https://github.com/bobthecow/psysh/wiki/Config-options).

| Option                    | Type                                  |
|---------------------------|---------------------------------------|
| `bracketed_paste`         | `bool`                                |
| `commands`                | `string`                              |
| `config_dir`              | `string`                              |
| `color_mode`              | `enum` {`auto`, `forced`, `disabled`} |
| `data_dir`                | `string`                              |
| `default_includes`        | `array` [`string`]                    |
| `erase_duplicates`        | `bool`                                |
| `error_logging_level`     | `string`                              |
| `history_file`            | `string`                              |
| `history_size`            | `int`                                 |
| `manual_db_file`          | `string`                              |
| `pager`                   | `string`                              |
| `pcntl`                   | `bool`                                |
| `require_semicolons`      | `bool`                                |
| `startup_message`         | `string`                              |
| `unicode`                 | `bool`                                |
| `use_tab_completion`      | `bool`                                |
| `matchers`                | `array` [`string`]                    |
| `variables`               | `array` [`string`]                    |
