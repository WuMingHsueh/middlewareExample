<?php

namespace Middleware\Middleware;

use Closure;
use Pimple\Container;
use Middleware\Service\Middleware\IMiddlewareLayer;

class End implements IMiddlewareLayer
{
	private $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	public function handle($request, Closure $next, $response)
	{
		$res = $next($request, $response);
		print 'End ' . PHP_EOL;
		return $res;
	}
}
