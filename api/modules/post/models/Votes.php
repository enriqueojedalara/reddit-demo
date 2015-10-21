<?php
/**
 * Post Votes, manage user votes database actions
 *
 * @package    Reddit_API
 * @subpackage Post_Models
 * @author     Enrique Ojeda <enriqueojedalara@gmail.com>
 */
namespace Modules\Post\Models;

use Phalcon\DI as DI;
use Phalcon\DI\FactoryDefault;
use Phalcon\Filter;

class Votes extends \Phalcon\Mvc\Model {

    /**
     * Post id
     * @access protected
     * @var Hex value
     */
	protected $id;

    /**
     * User id
     * @access protected
     * @var Hex value
     */
	protected $uid;

    /**
     * Vote direction (-1, 0 or 1)
     * @access protected
     * @var int
     */
	protected $vote;

     /**
     * Vote direction (-1, 0 or 1)
     * @access protected
     * @var int
     */
	protected $creation;


    /** 
     * Initialize model
     */
	public function initialize() {
	    $this->setSource('votes');
	}

    /** 
     * Set validations after fetch values
     */
	public function afterFetch() {
        $this->id = bin2hex($this->id);
        $this->uid = bin2hex($this->uid);
    }

    /** 
     * Set validations beforeexecute action for update/insert
     */
    public function beforeValidation() {
    	$this->id = pack("H*", $this->id);
    	$this->uid = pack("H*", $this->uid);
    }

    /**
     * Create or save votes
     * 
     * @override
     * @param array() Contain data to be used in sql, must be sent only id, uid and vote
     * @todo Improve this
     */
    public function save($data = NULL, $whiteList = NULL) {
    	array_push($data, $data['vote']);
    	$sql = 'INSERT INTO votes'
    		. ' SET'
    		. ' id = X?,'
    		. ' uid = X?,'
    		. ' vote = ?'
    		. ' ON DUPLICATE KEY UPDATE'
    		. ' vote = ?';
    	DI::getDefault()->get('db')->execute($sql, array_values($data));
    }

    /**
     * Getters everywhere ;)
     */
    public function __get($name) {
        if (isset($this->$name)) {
            return $this->$name;
        }
        return null;
    }
}