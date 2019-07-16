<?php
require __DIR__ . "/vendor/autoload.php";

$service = new StdClass();
$service->title = 'work';

use Pimple\Container;

$container = new Container();
$container['page'] = function ($c) use ($service) {
	$service->title = 'fuck';
	$service->body = 'Non velit et proident deserunt anim ut occaecat et nisi ipsum et magna.';
	return $service;
};
$show = $container['page'];
print $show->title;
