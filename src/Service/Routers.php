<?php

namespace BaseTemplatePHP\Service;

use Klein\klein;
use Klein\Request;
use Pimple\Container;
use BaseTemplatePHP\IEnvironment;

class Routers
{
	private $klein;
	private $kleinRequest;
	private $container;

	private $routers = [
		// ["method" => "post", 'path' => "", "controller" => "", "responseMethod" => "", "canActivate" => "" ],

	];

	private $routersPage = [
		// ["method" => "get", 'path' => "", "controller" => "", "responseMethod" => "", "viewLayout" => "", "viewRender" => ""],
	];

	public function __construct(Container $container = null)
	{
		$this->container = $container ?? new Container();
		$this->initSubDirectory(); // 若專案目錄是 "sub Directory" 則加入此函數設定$_SERVER['REQUEST_URI']

		$this->klein = new Klein;
		$this->respondAPI();
		$this->respondPage();
		$this->klein->dispatch($this->kleinRequest);

		// initSubDirectory function (2) content
		// $this->klein->dispatch();
	}

	private function initSubDirectory()
	{
		$this->kleinRequest = Request::createFromGlobals();
		$uri = $this->kleinRequest->server()->get('REQUEST_URI');
		$this->kleinRequest->server()->set('REQUEST_URI', substr($uri, strlen(IEnvironment::ROUTER_START)));

		// https://github.com/klein/klein.php/wiki/Sub-Directory-Installation
		//
		// (2)
		// This might also work,it doesn't need a custom request object
		// $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen(IEnvironment::ROUTER_START));
	}

	public function respondAPI()
	{
		foreach ($this->routers as $router) {
			$this->klein->respond($router['method'], $router['path'], function ($request, $resopnse) use ($router) {
				$controller = new $router['controller']($this->container);
				try {
					return $controller->{$router['responseMethod']}($request);
				} catch (\Exception $e) {
					$resopnse->code($e->getCode());
					return $e->getMessage();
				}
			});
		}
	}

	public function respondPage()
	{
		foreach ($this->routersPage as $routerPage) {
			$this->klein->respond($routerPage['method'], $routerPage['path'], function ($request, $resopnse, $service) use ($routerPage) {
				$controller = new $routerPage['controller']($this->container);
				$service = $controller->{$routerPage['responseMethod']}($request, $service);
			});
		}
	}
}
