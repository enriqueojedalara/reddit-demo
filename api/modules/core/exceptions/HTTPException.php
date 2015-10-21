<?php
/**
 * HTTP Exception
 *
 * @package    Reddit_API
 * @subpackage Core_Exceptions
 * @author     Enrique Ojeda <enriqueojedalara@gmail.com>
 */
namespace Modules\Core\Exceptions;


class HTTPException extends \Exception
{
	
	/**
	 * Response description
	 * @var string
	 */
	public $response;


	/**
	 * Construct
	 *
	 * @param string $message Error message
	 * @param int $code Error code (Should be based in HTTP code errors)
	 */
	public function __construct($message, $code)
	{
		$this->message = $message;
		$this->code = $code;
		$this->response = $this->getResponseDescription($code);
	}


	/**
	 * Send the Exception message using JSON response 
	 *
	 * @return HTTP response | true
	 */
	public function send() 
	{
		$di = \Phalcon\DI::getDefault();
		
		$res = $di->get('response');
		$req = $di->get('request');
		
		if( !$req->get('suppress_response_codes', null, null) ) {
			$res->setStatusCode($this->getCode(), $this->response)->sendHeaders();
		} 
		else {
			$res->setStatusCode( 200, 'OK' )->sendHeaders();
		}
		
		$error = array(
			'httpCode' => $this->getCode(), 
			'httpError' => $this->getMessage(),
		);
		
		if(!$req->get('type') || $req->get('type') == 'json'){
			$response = new \Modules\Core\Responses\JSONResponse();
			$response->send($error, true);
			return;
		}
		
		return true;
	}
	
	/**
	 * HTTP Error descriptions (See more in `http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html`)
	 *
	 * @param  int $code HTTP error code
	 *
	 * @return string HTTP Error description
	 */
	protected function getResponseDescription($code)
	{
		$codes = array(
			100 => 'Continue', 
			101 => 'Switching Protocols', 
			200 => 'OK', 
			201 => 'Created', 
			202 => 'Accepted', 
			203 => 'Non-Authoritative Information', 
			204 => 'No Content', 
			205 => 'Reset Content', 
			206 => 'Partial Content', 
			300 => 'Multiple Choices', 
			301 => 'Moved Permanently', 
			302 => 'Found', 
			303 => 'See Other', 
			304 => 'Not Modified', 
			305 => 'Use Proxy', 
			307 => 'Temporary Redirect', 
			400 => 'Bad Request', 
			401 => 'Unauthorized', 
			402 => 'Payment Required', 
			403 => 'Forbidden', 
			404 => 'Not Found', 
			405 => 'Method Not Allowed', 
			406 => 'Not Acceptable', 
			407 => 'Proxy Authentication Required', 
			408 => 'Request Timeout', 
			409 => 'Conflict', 
			410 => 'Gone', 
			411 => 'Length Required', 
			412 => 'Precondition Failed', 
			413 => 'Request Entity Too Large', 
			414 => 'Request-URI Too Long', 
			415 => 'Unsupported Media Type', 
			416 => 'Requested Range Not Satisfiable', 
			417 => 'Expectation Failed', 
			500 => 'Internal Server Error', 
			501 => 'Not Implemented', 
			502 => 'Bad Gateway', 
			503 => 'Service Unavailable', 
			504 => 'Gateway Timeout', 
			505 => 'HTTP Version Not Supported', 
			509 => 'Bandwidth Limit Exceeded'
		);
				
		return (isset($codes[$code])) ? $codes[$code] : 'Unknown Status Code';
	}
}
