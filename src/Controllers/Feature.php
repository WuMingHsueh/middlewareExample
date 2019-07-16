<?php

namespace Middleware\Controllers;

use Pimple\Container;

class Feature
{
	public function __construct(Container $container)
	{ }

	public function show($request, $response)
	{
		$request->dataPool->core = "主邏輯";
		print json_encode($request->dataPool, JSON_UNESCAPED_UNICODE);
	}
}
