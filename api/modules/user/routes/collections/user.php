<?php
/**
 * Define all routes to be used in module
 *
 * @package    Reddit_API
 * @subpackage User_Routes_Collections
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
	$handler = '\Modules\User\Controllers\UserController';

	/**
	 * Set prefix, handler and async process as true.
	 */
	$collection->setPrefix( '' )->setHandler( $handler )->setLazy( true );

	/**
	 * Define routes
	 */
	$collection->post( '/user', 'register' );
	$collection->post( '/login', 'login' );
	$collection->post( '/login/status', 'loginStatus' );
	
	return $collection;
} );
