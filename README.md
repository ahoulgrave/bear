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
```
<?php
require 'vendor/autoload.php';

$config = [
    'serviceManager' => [
        'services' => [
            MyController::class => new MyController(),
        ]
    ],
    'routing' => new SymfonyRoutingAdapter(),
];

$app = new App($config);
$app->run();

```

## Routing adapters
You need at least one routing adapter to run an application
- [Symfony Router Adapter](https://github.com/ahoulgrave/bear-routing-symfony)
- [FastRoute Router Adapter](https://github.com/ahoulgrave/bear-routing-fastroute)
