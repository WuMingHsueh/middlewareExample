<?php

namespace Middleware\Middleware;

use Closure;
use Pimple\Container;
use Middleware\Service\Middleware\IMiddlewareLayer;

class Firset implements IMiddlewareLayer
{
	private $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	public function handle($request, Closure $next, $response)
	{
		print 'firset ' . PHP_EOL;
		return $next($request, $response);
	}
}
