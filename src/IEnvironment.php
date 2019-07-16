<?php

namespace Middleware;

interface IEnvironment
{
	const PROJECT_NAME = "Middleware";
	const ROUTER_START = "/Middleware/index.php";
	const NAMESPACE_ROOT = "Middleware";
	const DOCUMENT_ROOT = "/usr/local/var/www";
	// const CONNECTION_NAME = ["default", "sqlserver_dverental"];

	const SESSION_PATH_NAME = [
		'LOGGIN' => ['PATH' => 'login', 'NAME' => 'data'],
		'CSRF'   => ['PATH' => 'form',  'NAME' => 'token']
	];
}
