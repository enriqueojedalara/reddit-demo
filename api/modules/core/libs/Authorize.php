<?php
/**
 * Manage access token authorizations
 *
 * @package    Reddit_API
 * @subpackage Core_Libs
 * @author     Enrique Ojeda <enriqueojedalara@gmail.com>
 */
namespace Modules\Core\Library;

use Phalcon\DI as DI;
use Phalcon\DI\FactoryDefault;
use Phalcon\Crypt;

class Authorize
{
	/**
	 * Constructor
	 *
	 * @param \Modules\User\Models\Users $user
	 * @return string Access token
	 */
	public static function createToken(\Modules\User\Models\Users $user)
	{
		$crypt = new Crypt;
		$di = DI::getDefault();

		$token = array();
		$token['uid'] = $user->uid;
		$token['username'] = $user->username;
		$token['address'] = $_SERVER['REMOTE_ADDR'];
		$token['agent'] = $_SERVER['HTTP_USER_AGENT'];
		$token['expiry'] = time() + $di->get('config')->app->tokenTimelife;
		$token = implode('|', $token);

		return bin2hex($crypt->encrypt($token, $di->get('config')->app->crypkey));
	}

	/**
	 * Validate if user is authorized based in header and access token
	 *
	 * @param \Modules\User\Models\Users $user
	 * @throws \Modules\Core\Exceptions\HTTPException
	 * @return true
	 */
	public static function isAuthorized()
	{
		//This method probably is not available if you use php-fpm
		//@todo, implement it 
		$headers = getallheaders();
	
		if (!isset($headers['Authorization'])){
			$msg = 'Token not provided';
			throw new \Modules\Core\Exceptions\HTTPException($msg, 401 );
		}

		$tokens = self::parseToken($headers['Authorization']);

		if ($tokens['address'] != $_SERVER['REMOTE_ADDR']){
			$msg = 'Wrong token (Ip address)';
			throw new \Modules\Core\Exceptions\HTTPException($msg, 401 );
		}

		if ($tokens['agent'] != $_SERVER['HTTP_USER_AGENT']){
			$msg = 'Wrong access token (User agent)';
			throw new \Modules\Core\Exceptions\HTTPException($msg, 401 );
		}

		if ($tokens['expiry'] < time()){
			$msg = 'Token has expiried';
			throw new \Modules\Core\Exceptions\HTTPException($msg, 401 );
		}
		
		return true;
	}

	/**
	 * Parse and get uid from access token.
	 *
	 * @throws \Modules\Core\Exceptions\HTTPException
	 * @return string uid (User id)
	 */
	public static function getUid() 
	{
		//This method probably is not available if you use php-fpm
		//@todo, implement it 
		$headers = getallheaders();

		if (!isset($headers['Authorization'])){
			$msg = 'Token not provided';
			throw new \Modules\Core\Exceptions\HTTPException($msg, 401 );
		}

		$tokens = self::parseToken($headers['Authorization']);
		return $tokens['uid'];
	}

	/**
	 * Parse and get username from access token.
	 *
	 * @throws \Modules\Core\Exceptions\HTTPException
	 * @return string username
	 */
	public static function getUsername() 
	{
		//This method probably is not available if you use php-fpm
		//@todo, implement it 
		$headers = getallheaders();

		if (!isset($headers['Authorization'])){
			$msg = 'Token not provided';
			throw new \Modules\Core\Exceptions\HTTPException($msg, 401 );
		}

		$tokens = self::parseToken($headers['Authorization']);
		return $tokens['username'];
	}

	/**
	 * Parse and get username from access token.
	 *
	 * @access protected
	 * @param string $accessToken
	 * @throws \Modules\Core\Exceptions\HTTPException
	 * @return array tokens (Parts of access token)
	 */
	protected static function parseToken($accessToken = '') 
	{
		$crypt = new Crypt;
		$di = DI::getDefault();

		set_error_handler( function() { 
			$msg = 'Invalid access token';
			throw new \Modules\Core\Exceptions\HTTPException($msg, 401);
		} );

		$accessToken = pack('H*', $accessToken);
		$accessToken = $crypt->decrypt($accessToken, $di->get('config')->app->crypkey);
		$parts = explode('|', $accessToken);

		if (!is_array($parts) || count($parts) != 5) {
			$msg = 'Invalid access token, tokens do not match';
			throw new \Modules\Core\Exceptions\HTTPException($msg, 401 );
		}

		$tokens = array();
		$tokens['uid'] = $parts[0];
		$tokens['username'] = $parts[1];
		$tokens['address'] = $parts[2];
		$tokens['agent'] = $parts[3];
		$tokens['expiry'] = $parts[4];

		return $tokens;
	}
}
