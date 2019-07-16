<?php
require __DIR__ . "/vendor/autoload.php";

use Middleware\Service\Middleware\API\Onion as OnionAPI;
use Pimple\Container;

$container = new Container();
$middlewares = [  // 不論 in 或是 out 外層結構先寫
	"Middleware\Middleware\Firset",
	"Middleware\Middleware\End",
	"Middleware\Middleware\Before",
	"Middleware\Middleware\After",
];
$onion = new OnionAPI(\array_map(function ($class) use ($container) {
	return new $class($container);
}, $middlewares));
$onion->handle('request', function ($request, $response) {
	print "get $request show $response" . PHP_EOL;
}, 'response');
