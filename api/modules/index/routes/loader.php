<?php
/**
 * Loads a set of Phalcon Mvc\Micro\Collections from the collections directory.
 * Return an anonymous function which returns a set of Collections
 *
 * @package    Reddit_API
 * @subpackage Index_Routes
 * @author     Enrique Ojeda <enriqueojedalara@gmail.com>
 */
return call_user_func( function () 
{	
	/**
	 * Collection containers
	 */
	$collections = array();
	
	/**
	 * Collections in collections folder
	 */
	$files = scandir( dirname( __FILE__ ) . '/collections' );
	

	$base = dirname( __FILE__ );

	/**
	 * Extract all collections
	 */
	foreach( $files as $file )
	{
		$pathinfo = pathinfo( $file );
		if( $pathinfo['extension'] === 'php' )
		{
			$collections[] = include( $base . '/collections/' . $file );
		}
	}
	return $collections;
} );
