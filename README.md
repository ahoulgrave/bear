# Bear PHP Component Project
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/4a5737d0-e7d7-489b-a0a6-cece6cffc6fa/mini.png)](https://insight.sensiolabs.com/projects/4a5737d0-e7d7-489b-a0a6-cece6cffc6fa)
[![Build Status](https://travis-ci.org/ahoulgrave/bear.svg?branch=master)](https://travis-ci.org/ahoulgrave/bear)
[![Coverage Status](https://coveralls.io/repos/github/ahoulgrave/bear/badge.svg?branch=master)](https://coveralls.io/github/ahoulgrave/bear?branch=master)
[![Latest Stable Version](https://poser.pugx.org/ahoulgrave/bear/v/stable)](https://packagist.org/packages/ahoulgrave/bear)
[![Total Downloads](https://poser.pugx.org/ahoulgrave/bear/downloads)](https://packagist.org/packages/ahoulgrave/bear)

## Requirements
- PHP 7.1+

## Installation
You can install bear using composer
```
composer require ahoulgrave/bear
```

## Usage
```php
<?php
require 'vendor/autoload.php';

use Bear\App;
use Bear\Routing\SymfonyRoutingAdapter;
use Zend\ServiceManager\ServiceManager;

$config = [
    'services' => [
        MyController::class => new MyController(),
    ],
];

$app = new App(new ServiceManager($config), new SymfonyRoutingAdapter($loader, $resource));
$app->run();
```

## Dependency containers
As first argument, you can provide any PSR-11 Container. Here are some you can use:
- [Zend Service Manager](https://docs.zendframework.com/zend-servicemanager/)
- [Symfony DI Component](http://symfony.com/doc/current/components/dependency_injection.html)
- [Pimple](https://pimple.symfony.com/)
- [Aura.DI](https://github.com/auraphp/Aura.Di)
- [PHP DI](http://php-di.org/)
- [PHP League's Container](http://container.thephpleague.com/)

## Routing adapters
You need at least one routing adapter to run an application
- [Symfony Routing Adapter](https://github.com/ahoulgrave/bear-routing-symfony)
- [FastRoute Routing Adapter](https://github.com/ahoulgrave/bear-routing-fastroute)
