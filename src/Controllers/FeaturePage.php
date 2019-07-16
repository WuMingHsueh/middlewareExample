<?php

namespace Middleware\Controllers;

use Pimple\Container;
use Middleware\IEnvironment;
use stdClass;

class FeaturePage
{
	private $page;

	public function __construct(Container $container)
	{
		$this->page = $container['page'];
	}

	public function show($request, $response)
	{
		$this->page->routerRoot = IEnvironment::ROUTER_START;
		$this->page->title = '鳥蛋';
		$this->page->__set('dataPool', new stdClass);
		$this->page->dataPool->core = '鳥蛋層';
		$this->page->layout("src/Views/layouts/default.php");
		$this->page->render("src/Views/auth/signIn.php");
	}

	public function showPage($request, $response)
	{
		$this->page->routerRoot = IEnvironment::ROUTER_START;
		$this->page->title = '鳥蛋';
		$this->page->dataPool->core = '鳥蛋層';
		$this->page->layout("src/Views/layouts/default.php");
		$this->page->render("src/Views/auth/signIn.php");
	}

	public function showPageAfter($request, $response)
	{
		$this->page->routerRoot = IEnvironment::ROUTER_START;
		$this->page->title = '鳥蛋';
		$this->page->__set('dataPool', new stdClass);
		$this->page->dataPool->core = '鳥蛋層';
		$this->page->layout("src/Views/layouts/default.php");
	}
}
