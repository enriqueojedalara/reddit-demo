<?php
/**
 * Module (Module main loader)
 *
 * @package    Reddit_API
 * @subpackage Core
 * @author     Enrique Ojeda <enriqueojedalara@gmail.com>
 */
namespace Modules\Core;

use Phalcon\DI as DI;
use Phalcon\DI\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Crypt;
use Phalcon\Db\Adapter\Pdo\Mysql as Database;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File as FileAdapter;
use Phalcon\Events\Manager as EventsManager;

$logger = new FileAdapter('debug.log');

/**
 * Used to debug sqls executed in Models (We will never expose technical errors to users)
 */
class Listener
{
    public function beforeQuery($event, $connection)
    {
    	$logger = new FileAdapter('debug.log');
        $logger->log($connection->getSQLStatement(), \Phalcon\Logger::INFO, $connection->getSQLVariables());
    }
}

class Module
{
	/**
	 * Module name
	 */
	const MODULE = 'core';

	/**
	 * Global config file
	 */
	const CONFIG_FILE = 'config/config.ini';


	/**
	 * Constructor
	 */
	public function __construct() 
	{
		$di = DI::getDefault();
		$this->registerAutoloaders(new Loader);
		$this->registerConfigurations($di);
		$this->registerServices($di);
	}
	
	/**
	 * Register autoloaders
	 * @param  Loader $loader
	 */
	private function registerAutoloaders(Loader $loader)
	{
		$module = ucfirst(self::MODULE);
		$loader->registerNamespaces(array(
			'Modules\\' . $module . '\\Controllers' => 'modules/' . self::MODULE . '/controllers/', 
			'Modules\\' . $module . '\\Models' => 'modules/' . self::MODULE . '/models/', 
			'Modules\\' . $module . '\\Responses' => 'modules/' . self::MODULE . '/responses/', 
			'Modules\\' . $module . '\\Exceptions' => 'modules/' . self::MODULE . '/exceptions/', 
			'Modules\\' . $module . '\\Library' => 'modules/' . self::MODULE . '/libs/',
		))->register();
	}
	
	/**
	 * Register configurations
	 * @param  Phalcon\DI $di
	 */
	private function registerConfigurations($di)
	{
		$di->setShared('config', function () {
			return new \Phalcon\Config\Adapter\Ini(self::CONFIG_FILE);
		});
	}
	
	/**
	 * Register cache, database, auth object and global requestBody (For HTTP body)
	 * @param  Phalcon\DI $di
	 * @throws \Modules\Core\Exceptions\HTTPException
	 * @return void
	 */
	private function registerServices($di)
	{
		/**
		 * Starting cache (Memcached)
		 */
		$di->set('cache', function () {
			$di = DI::getDefault();
			$frontCache = new \Phalcon\Cache\Frontend\Data(array('lifetime' => $di->get('config')->memcached->lifetime));
			$cache = new \Phalcon\Cache\Backend\Libmemcached($frontCache, 
				array('servers' => array(
					'host' => $di->get('config')->memcached->host, 
					'port' => $di->get('config')->memcached->port, 
					'weight' => $di->get('config')->memcached->weight,)
				) 
			);
			return $cache;
		});
		
		/**
		 * Initialize database object
		 */
		$di->set('db', function () {
			$di = DI::getDefault();

			$adapter = new Database(array(
				'host' => $di->get('config')->db->host, 
				'username' => $di->get('config')->db->username, 
				'password' => $di->get('config')->db->password, 
				'dbname' => $di->get('config')->db->dbname) 
			);

			// Added to log and debug sql queries
			$eventsManager = new \Phalcon\Events\Manager();
			$eventsManager->attach('db', new Listener());
			$adapter->setEventsManager($eventsManager);

			return $adapter;
		});
		
		/**
		 * Service to read HTTP body from requests
		 */
		$di->setShared('requestBody', function () {
			$in = file_get_contents('php://input');
			$in = json_decode($in, false);
			
			if($in === null)
			{
				throw new \Modules\Core\Exceptions\HTTPException('There was a problem understanding the data sent to the server by the application.', 409, array('dev' => 'The JSON body sent to the server was unable to be parsed.', 'appCode' => 'REQ1000', 'more' => ''));
			}
			return $in;
		});


		/**
		 * Service to use authin a global way
		 */
		$di->setShared('auth', function() {

			$di = DI::getDefault();
			$crypt = new Crypt;

			//This method probably is not available if you use php-fpm
    		//@todo, implement it 
			$headers = getallheaders();
		
			if (!isset($headers['Authorization'])){
				$msg = 'Token not provided';
				throw new \Modules\Core\Exceptions\HTTPException($msg, 401);
			}

			set_error_handler(function() { 
				$msg = 'Invalid access token';
				throw new \Modules\Core\Exceptions\HTTPException($msg, 401);
			});

			$token = pack('H*', $headers['Authorization']);			

			if (!$token){
				$msg = 'Wrong access token';
				throw new \Modules\Core\Exceptions\HTTPException($msg, 401);
			}

			$token = $crypt->decrypt($token, $di->get('config')->app->crypkey);

			$tokens = explode('|', $token);
			if ($tokens[2] != $_SERVER['REMOTE_ADDR']){
				$msg = 'Wrong token (Ip address)';
				throw new \Modules\Core\Exceptions\HTTPException($msg, 401);
			}

			if ($tokens[3] != $_SERVER['HTTP_USER_AGENT']){
				$msg = 'Wrong access token (User agent)';
				throw new \Modules\Core\Exceptions\HTTPException($msg, 401);
			}

			if ($tokens[4] < time()){
				$msg = 'Token has expiried';
				throw new \Modules\Core\Exceptions\HTTPException($msg, 401);
			}
			
			return true;
		});

	}
}
