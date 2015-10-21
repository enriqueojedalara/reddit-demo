<?php
/**
 * User model
 *
 * @package    Reddit_API
 * @subpackage User_Models
 * @author     Enrique Ojeda <enriqueojedalara@gmail.com>
 */
namespace Modules\User\Models;

use Phalcon\DI as DI;
use Phalcon\DI\FactoryDefault;
use Phalcon\Filter;

class Users extends \Phalcon\Mvc\Model {

	/**
	 * User id
	 * @access protected
	 * @var Hex value
	 */
	protected $uid;

	/**
	 * Username
	 * @access protected
	 * @var string
	 */
	protected $username;

	/**
	 * Password (Always have to be encrypted)
	 * @access protected
	 * @var string
	 */
	protected $password;

	/**
	 * Email
	 * @access protected
	 * @var string
	 */
	protected $email;

	/**
	 * Creation date
	 * @access protected
	 * @var int
	 */
	protected $creation;

	/** 
	 * Initialize model
	 */
	public function initialize() {
	    $this->setSource('users');
        $this->skipAttributesOnCreate(array('uid'));
        $this->skipAttributesOnUpdate(array('uid'));
	}

	/** 
	 * Check if username is duplicated in database
	 * 
	 * @param string $username
	 * @return boolean
	 */
	public function isDuplicated($username = '') {
		$sql = 'SELECT HEX(uid) from `users` WHERE `username` = ?';
		$rs = DI::getDefault()->get('db')->query($sql, array($username));
		return $rs->numRows() > 0;
	}

	/** 
	 * Set validations after fetch values
	 */
	public function afterFetch() {
        $this->uid = bin2hex($this->uid);
        $this->creation = strtotime($this->creation);
    }

    /**
     * I see getters everywhere, I will go for other coffee cup (Yeah!)
     */
	public function __get($name) {
        if (isset($this->$name)) {
            return $this->$name;
        }
        return null;
    }
}