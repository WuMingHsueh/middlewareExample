<?php

namespace Middleware\Middleware;

use Closure;
use Pimple\Container;
use Middleware\Service\Middleware\IMiddlewareLayer;

class AfterPage implements IMiddlewareLayer
{
	private $page;

	public function __construct(Container $container)
	{
		$this->page = $container['page'];
	}

	public function handle($request, Closure $next, $response)
	{
		$res = $next($request, $response);
		$this->page->dataPool->after = '最後一道防線';
		$this->page->layout("src/Views/layouts/defaultShow.php");
		$this->page->render("src/Views/auth/signIn.php");
		return $res;
	}
}
