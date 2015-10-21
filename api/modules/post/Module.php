<?php
/**
 * Module (Module post loader)
 *
 * @package    Reddit_API
 * @subpackage Post
 * @author     Enrique Ojeda <enriqueojedalara@gmail.com>
 */
namespace Modules\Post;

use Phalcon\DI as DI;
use Phalcon\Loader;

class Module
{
	/**
	 * Module name
	 */
	const MODULE = 'post';

	/**
	 * Constructor
	 */
	public function __construct() 
	{
		$di = DI::getDefault();
		$this->registerAutoloaders( new Loader );
		$this->registerRouters( $di );
	}
	
	/**
	 * Register autoloaders
	 *
	 * @access private
	 * @param  Loader $loader
	 */
	private function registerAutoloaders( Loader $loader )
	{
		$module = ucfirst( self::MODULE );
		$loader->registerNamespaces( array(
			'\Modules\\' . $module . '\\Controllers' => 'modules/' . self::MODULE . '/controllers/', 
			'\Modules\\' . $module . '\\Models' => 'modules/' . self::MODULE . '/models/', 
		) )->register();
	}
	
	/**
	 * Register routers
	 *
	 * @access private
	 * @param  Phalcon\DI $di
	 * @return void
	 */
	private function registerRouters( $di )
	{
		$di->set( 'collections_' . self::MODULE, function () {
			return include( __DIR__ . '/routes/loader.php' );
		} );
	}
}
