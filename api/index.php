<?php
$start_timestamp = microtime();

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Micro\Collection;
use Phalcon\Config\Adapter\Ini;
use Phalcon\Loader;
use Phalcon\Mvc\Application;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File as FileAdapter;

header('Access-Control-Allow-Origin: *');

/**
 * Logger file
 */
$logger = new FileAdapter('debug.log');

/**
 * Direct Injector (Singleton instance)
 * Important to instance it before everything to keep the singleton instance.
 */
$di = Phalcon\DI::getDefault();
$di = new FactoryDefault;

/**
 * Module namespaces. If you will add more modules, add them here first.
 */
$modules = array(
	'Modules\Core' => __DIR__ . '/modules/core',
	'Modules\Index' => __DIR__ . '/modules/index',
	'Modules\User' => __DIR__ . '/modules/user',
	'Modules\Post' => __DIR__ . '/modules/post',
);

/**
 * Creates the autoloader and register namespaces of each module
 */
$loader = new Loader;
$loader->registerNamespaces($modules)->register();

/**
 * Initialize each module executing Module.php of each one.
 * Save module name to be used when routes will be registered.
 */
$modulesName = array();
foreach($modules as $namespace => $path){
	$modulesName[] = basename($path);
	$module = $namespace.'\\Module';
	$module = new $module;
}

$app = new Phalcon\Mvc\Micro;
$app->setDI($di);

/**
 * Add exception handler to show them in json 
 */
set_exception_handler(function($e) use ($app){
	$logger = new FileAdapter('debug.log');
	$logger->warning($e->getMessage());
	if (method_exists($e, 'send')){
		$e->send();
	}
	$logger->error($e->getTraceAsString());
});


/**
 * Mount all collections (Makes routes active).
 * @todo improve this block of code
 */
foreach ($modulesName as $module){
	$routeDefinitions = array('GET'=>array(), 'POST'=>array(), 'PUT'=>array(), 'DELETE'=>array());
	
	try{
		foreach($di->get('collections_'.$module) as $collection){
			$app->mount($collection);
			$routes = $app->getRouter()->getRoutes();
			foreach($routes as $route){
				$pattern = $route->getPattern();
				$method = $route->getHttpMethods();
				if (array_search($pattern, $routeDefinitions[$method]) !== false){
					$msg = 'Route: ' . $method. ' '.$pattern.'" is already implemented (Duplicated)';
					throw new \Modules\Core\Exceptions\RouteException($msg, 500);
				}
				$routeDefinitions[$method][] = $pattern;
			}
		}
	}
	catch(Exception $e){
		$logger->warning($e->getMessage());
	}	
}

/**
 * After a route is run, usually when its Controller returns a final value,
 * the application runs the following function which actually sends the response to the client.
 *
 * The default behavior is to send the Controller's returned value to the client as JSON.
 * However, by parsing the request querystring's 'type' paramter, it is easy to install
 * different response type handlers.  Below is an alternate csv handler.
 */
$app->after(function() use ($app) {
	$di = \Phalcon\DI::getDefault();
	
	// OPTIONS have no body, send the headers, exit
	if($app->request->getMethod() == 'OPTIONS'){
		$app->response->setStatusCode('200', 'OK');
		$app->response->send();
		return;
	}

	$res = $app->getReturnedValue();
	$in = json_encode($res, FALSE);
	if($in === null){
		$response = $di->get('response');
		$response->setContent($res);
		$response->send();
		return;
	}

	if(!$app->request->get('type') || $app->request->get('type') == 'json'){
		$response = new \Modules\Core\Responses\JSONResponse();
		$response->send($res);	
		return;
	}
	else {
		throw new \Modules\Core\Exceptions\HTTPException('Could not return results in specified format', 403);
	}
});

/**
 * Default handler when route is not found.
 */
$app->notFound(function () use ($app) {
	throw new \Modules\Core\Exceptions\HTTPException('Not Found.', 404);
});

/**
 * Run everything
 */
$app->handle();

