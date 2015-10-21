<?php
/**
 * Base controller
 *
 * @package    Reddit_API
 * @subpackage Core_Controllers
 * @author     Enrique Ojeda <enriqueojedalara@gmail.com>
 */
namespace Modules\Core\Controllers;

use Phalcon\Mvc\Controller;

class BaseController extends \Phalcon\DI\Injectable 
{
	
	/** 
	* Constructor
	*/
	public function __construct()
	{
		$di = \Phalcon\DI::getDefault();
		$this->setDI( $di );
	}


	/** 
	* Validate required files in request HTTP body
	*
	* @param array $fields 
	* @throws \Modules\Core\Exceptions\HTTPException
	*/
	protected function validateRequireFields($fields = array())
	{
		foreach($fields as $field)
		{
			if (!isset($this->getDI()->get('requestBody')->$field))
			{
				throw new \Modules\Core\Exceptions\HTTPException($field . ' is required', 400);
			}
		}
	}
}
