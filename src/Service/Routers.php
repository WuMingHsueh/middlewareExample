<?php

namespace Middleware\Service;

use Klein\klein;
use Klein\Request;
use Pimple\Container;
use Middleware\IEnvironment;
use Middleware\Service\Middleware\Onion;
use Middleware\Service\Middleware\API\Onion as OnionAPI;
use Middleware\Service\Middleware\Page\Onion as OnionPage;

class Routers
{
	private $klein;
	private $kleinRequest;
	private $container;

	private $routers = [
		// ["method" => "post", 'path' => "", "controller" => "", "responseMethod" => "", "middlewareLayers" => [] ],
		["method" => "get", 'path' => "/before", "controller" => "Middleware\Controllers\Feature", "responseMethod" => "show", "middlewareLayers" => ['Middleware\Middleware\before']],
		["method" => "get", 'path' => "/after", "controller" => "Middleware\Controllers\Feature", "responseMethod" => "show",  "middlewareLayers" => ['Middleware\Middleware\after', 'Middleware\Middleware\before', "Middleware\Middleware\Firset", "Middleware\Middleware\End"]],
	];

	private $routersPage = [
		// ["method" => "get", 'path' => "", "controller" => "", "responseMethod" => "", "viewLayout" => "", "viewRender" => "", "middlewareLayers" => []],
		["method" => "get", 'path' => "/page", "controller" => "Middleware\Controllers\FeaturePage", "responseMethod" => "show", "middlewareLayers" => []],
		["method" => "get", 'path' => "/beforePage", "controller" => "Middleware\Controllers\FeaturePage", "responseMethod" => "showPage", "middlewareLayers" => ['Middleware\Middleware\BeforePage']],
		["method" => "get", 'path' => "/afterPage", "controller" => "Middleware\Controllers\FeaturePage", "responseMethod" => "showPageAfter",  "middlewareLayers" => ['Middleware\Middleware\AfterPage']],
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
			$this->klein->respond($router['method'], $router['path'], function ($request, $response) use ($router) {
				$controller = new $router['controller']($this->container);
				try {
					if ((empty($router['middlewareLayers']))) {
						return call_user_func([$controller, $router['responseMethod']], $request, $response);
					} else {
						return $this->provideMiddleware(
							$router['middlewareLayers'],
							$this->container,
							$request,
							$response,
							$controller,
							$router['responseMethod']
						);
					}
				} catch (\Exception $e) {
					$response->code($e->getCode());
					return $e->getMessage();
				}
			});
		}
	}

	public function respondPage()
	{
		foreach ($this->routersPage as $routerPage) {
			$this->klein->respond($routerPage['method'], $routerPage['path'], function ($request, $response, $service) use ($routerPage) {
				$this->container['page'] = function ($c) use ($service) {
					return  $service;
				};
				$controller = new $routerPage['controller']($this->container);
				if ((empty($routerPage['middlewareLayers']))) {
					call_user_func([$controller, $routerPage['responseMethod']], $request, $response);
				} else {
					$this->provideMiddleware(
						$routerPage['middlewareLayers'],
						$this->container,
						$request,
						$response,
						$controller,
						$routerPage['responseMethod']
					);
				}
				$service = $this->container['page'];
			});
		}
	}

	private function provideMiddleware(array $middlewares, $container, $request, $response, $controller, $method)
	{
		// 創建 onion 並在各層中注入相依物件
		$onion = new Onion(\array_map(function ($class) use ($container) {
			return new $class($container);
		}, $middlewares));

		// 依序執行個中介層邏輯
		return $onion->handle($request, function ($request, $response) use ($controller, $method) {
			return $controller->{$method}($request, $response);
		}, $response);
	}

	private function generatorAPIMiddleware(array $middlewares, Container $container, $request, $response, $controller, $method)
	{
		// 創建 onion 物件建立各層結構 並匯入各層物件
		$onion = new OnionAPI(\array_map(function ($class) use ($container) {
			return new $class($container);
		}, $middlewares));

		// 遍訪執行各層程式
		return $onion->handle($request, function ($request, $response) use ($controller, $method) {
			return $controller->{$method}($request, $response);
		}, $response);
	}

	private function generatorPageMiddleware(array $middlewares, $container, $request, $response, $service, $controller, $method)
	{
		// 注入相依物件到每個中介曾物件中
		$onion = new OnionPage(\array_map(function ($class) use ($container) {
			return new $class($container);
		}, $middlewares));
		// 執行每層中介層
		return $onion->handle($request, function ($request, $response, $service) use ($controller, $method) {
			return $controller->{$method}($request, $response, $service);
		}, $response, $service);
	}
}
