<?php
/**
 * Define all routes to be used in module
 *
 * @package    Reddit_API
 * @subpackage Post_Routes_Collections
 * @author     Enrique Ojeda <enriqueojedalara@gmail.com>
 */
return call_user_func( function () 
{
	/**
	 * Instance collection from Phalcon Mvc
	 */
	$collection = new \Phalcon\Mvc\Micro\Collection();

	/**
	 * Define handler (Controller)
	 */
	$handler = '\Modules\Post\Controllers\PostController';

	/**
	 * Set prefix, handler and async process as true.
	 */
	$collection->setPrefix('')->setHandler( $handler )->setLazy( true );

	/**
	 * Define urls (Routes) and link them with controller
	 */
	$collection->post('/post/submit', 'submit');
	$collection->get('/posts/{page:[0-9]+}', 'fetch');
	$collection->get('/posts', 'fetch');
	$collection->post('/post/vote', 'vote');
	
	return $collection;
} );
