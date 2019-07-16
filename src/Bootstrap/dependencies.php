<?php

use Pimple\Container;

$container = new Container();
$container->register(new Middleware\Service\Session\SessionService);
