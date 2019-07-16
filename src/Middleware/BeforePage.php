<?php

namespace Middleware\Middleware;

use Closure;
use Pimple\Container;
use Middleware\Service\Middleware\IMiddlewareLayer;
use stdClass;

class BeforePage implements IMiddlewareLayer
{
	private $page;

	public function __construct(Container $container)
	{
		$this->page = $container['page'];
	}

	public function handle($request, Closure $next, $response)
	{
		$this->page->__set('dataPool', new stdClass);
		$this->page->dataPool->before = '中介層來了';
		return $next($request, $response);
	}
}
