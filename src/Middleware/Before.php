<?php

namespace Middleware\Middleware;

use Closure;
use Pimple\Container;
use Middleware\Service\Middleware\IMiddlewareLayer;
use stdClass;

class Before implements IMiddlewareLayer
{
	private $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	public function handle($request, Closure $next, $response)
	{
		$request->__set('dataPool', new stdClass);
		$request->dataPool->before = ["USD", "JPY", "TWD"];
		return $next($request, $response);
	}
}
