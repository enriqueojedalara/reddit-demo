<?php
/**
 * Define all routes to be used in module
 *
 * @package    Reddit_API
 * @subpackage Index_Routes_Collections
 * @author     Enrique Ojeda <enriqueojedalara@gmail.com>
 */

return call_user_func( function () 
{
	/**
	 * Define handler (Controller)
	 */
	$handler = '\Modules\Index\Controllers\IndexController';

	/**
	 * Instance collection from Phalcon Mvc
	 */
	$collection = new \Phalcon\Mvc\Micro\Collection();


	/**
	 * Set prefix, handler and async process as false.
	 */
	$collection->setPrefix( '' )->setHandler( $handler )->setLazy( true );

	/**
	 * Define urls (Routes) and link them with controller
	 */
	$collection->get( '/', 'index' );
	$collection->get( '/version', 'version' );
	$collection->post( '/version', 'version' );

	return $collection;
} );
