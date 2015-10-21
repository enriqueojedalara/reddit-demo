<?php
/**
 * User controller, manage posts actions
 *
 * @package    Reddit_API
 * @subpackage User_Controllers
 * @author     Enrique Ojeda <enriqueojedalara@gmail.com>
 */
namespace Modules\User\Controllers;

use Phalcon\Filter;
use Phalcon\Crypt;
use Modules\Core\Controllers\BaseController;
use Modules\User\Models\Users;


class UserController extends BaseController
{

	/** 
	* Constructor
	*/
	public function __construct() 
	{
		parent::__construct();
	}

	/** 
	 * Signup action (Create a new user), data came from HTTP Body as JSON
	 *
	 * @return true 
	 * @throws \Modules\Core\Exceptions\HTTPException
	 */
	public function register() 
	{
		$this->validateRequireFields(array('username', 'password', 'email'));

		$filter = new Filter;
		$params = array(
			'username' => $filter->sanitize($this->getDI()->get('requestBody')->username, 'string'),
			'password' => $this->security->hash($this->getDI()->get('requestBody')->password),
			'email' => $filter->sanitize($this->getDI()->get('requestBody')->email, 'email'),
		);

		$user = new Users;
		$users = Users::find("username = '".$params['username']."'");
		if ($users->count() > 0){
			$msg = 'Username already exists';
			throw new \Modules\Core\Exceptions\HTTPException($msg, 409 );
		}
		
		if ($user->save($params) === false){
			$messages = array();
			foreach ($post->getMessages() as $msg) {
				$messages[] = $msg;
			}
			throw new \Modules\Core\Exceptions\HTTPException($messages, 500);
		}
		return true;
	}

	/** 
	 * Signin action (Login), data came from HTTP Body as JSON
	 *
	 * @throws \Modules\Core\Exceptions\HTTPException
	 * @return array(Access token) This will be converted to JSON format 
	 */
	public function login() 
	{
		$this->validateRequireFields(array('username', 'password'));

		$isUsernameEmail = true;
		if (filter_var($this->getDI()->get('requestBody')->username, FILTER_VALIDATE_EMAIL) === false) {
			$isUsernameEmail = false;
		}

		$filter = new Filter;
		$params = array(
			'username' => $filter->sanitize($this->getDI()->get('requestBody')->username, ($isUsernameEmail ? 'email' : 'string')),
			'password' => $this->security->hash($this->getDI()->get('requestBody')->password),
		);

		$user = new Users;

		$users = Users::find("username = '".$params['username']."'");
		if ($users->count() <= 0){
			$msg = 'User not found';
			throw new \Modules\Core\Exceptions\HTTPException($msg, 404);
		}

		if ($this->security->checkHash($params['password'], $users->getFirst()->password)){
			$msg = 'Wrong credentials to login';
			throw new \Modules\Core\Exceptions\HTTPException($msg, 403 );
		}

		return array(
			'access_token' => \Modules\Core\Library\Authorize::createToken($users->getFirst()),
		);
	}

	/** 
	 * Check login status
	 *
	 * @throws \Modules\Core\Exceptions\HTTPException
	 * @return string 
	 */
	public function loginStatus() 
	{
		//Check if user is authorized
		\Modules\Core\Library\Authorize::isAuthorized();
		return md5(uniqid());
	}
}