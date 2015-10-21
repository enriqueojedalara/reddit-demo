<?php
/**
 * Route Exception (Used internally for routing process)
 *
 * @package    Reddit_API
 * @subpackage Core_Exceptions
 * @author     Enrique Ojeda <enriqueojedalara@gmail.com>
 */
namespace Modules\Core\Exceptions;

class RouteException extends HTTPException{

	/**
	 * Constructor
	 *
	 * @param string $message Error message
	 * @param int $code Error code
	 */
	public function __construct($message, $code)
	{
		parent::__construct($message, $code);
		$this->message = $message;
		$this->code = $code;
	}
}