<?php

namespace Middleware\Service\Middleware;

use Closure;

interface IMiddlewareLayer
{
	public function handle($request, Closure $next, $response);
}
