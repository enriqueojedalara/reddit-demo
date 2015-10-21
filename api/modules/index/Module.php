<?php
/**
 * Module (Module index loader)
 *
 * @package    Reddit_API
 * @subpackage Index
 * @author     Enrique Ojeda <enriqueojedalara@gmail.com>
 */
namespace Modules\Index;

use Phalcon\DI as DI;
use Phalcon\DI\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Db\Adapter\Pdo\Mysql as Database;

class Module
{
	/**
	 * Module name
	 */
	const MODULE = 'index';

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
	 * @param  Loader $loader
	 * @return void
	 */
	private function registerAutoloaders( Loader $loader )
	{
		$module = ucfirst( self::MODULE );
		$loader->registerNamespaces( array(
			'Modules\\' . $module . '\\Controllers' => 'modules/' . self::MODULE . '/controllers/', 
		) )->register();
	}
	
	/**
	 * Register routers
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
