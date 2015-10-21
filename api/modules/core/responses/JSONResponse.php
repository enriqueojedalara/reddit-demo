<?php
/**
 * JSON response (Send responses in json format)
 *
 * @package    Reddit_API
 * @subpackage Core_Responses
 * @author     Enrique Ojeda <enriqueojedalara@gmail.com>
 */
namespace Modules\Core\Responses;


class JSONResponse extends Response
{

	/**
	 * Constructor
	 */
	public function __construct() 
	{
		parent::__construct();
	}
	
	/**
	 * Send response as json
	 * @param  mixed $message Response
	 * @param  bool $error Is error response?
	 * @return JSONResponse response
	 */
	public function send($message, $error = false)
	{
		$response = $this->di->get('response');
		$request = $this->di->get('request');
		$success = ($error) ? 'ERROR' : 'SUCCESS';
		
		$response->setHeader('E-Tag', md5(serialize($message)));
		$response->setHeader('X-Record-Count', count($message));
		$response->setHeader('X-Status', $success);
		
		//ON IE seems there is a bug and needs return as text/html.
		// But to be honest I really hate IE so I not add the header :D
		$response->setContentType( 'application/json; charset=UTF-8' );
		
		if( !$this->head ) {
			if (!is_array($message) && !is_object($message)) {
				$message = array('res' => $message);
			}
			$response->setJsonContent($message);
		}
		
		$response->send();
		
		return $this;
	}
}
