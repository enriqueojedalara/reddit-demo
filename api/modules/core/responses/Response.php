<?php
/**
 * HTTP Response
 *
 * @package    Reddit_API
 * @subpackage Core_Responses
 * @author     Enrique Ojeda <enriqueojedalara@gmail.com>
 */
namespace Modules\Core\Responses;


class Response extends \Phalcon\DI\Injectable
{

	protected $head = false;

	/**
	 * Constructor
	 */
	public function __construct(){
		$di = \Phalcon\DI::getDefault();
		$this->setDI($di);

		if(strtolower($this->di->get('request')->getMethod()) === 'head'){
			$this->head = true;
		}
	}
}
