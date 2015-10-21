<?php
/**
 * Index controller (Manage all actions)
 *
 * @package    Reddit_API
 * @subpackage Index_Controllers
 * @author     Enrique Ojeda <enriqueojedalara@gmail.com>
 */
namespace Modules\Index\Controllers;

use Modules\Core\Controllers\BaseController;


class IndexController extends BaseController
{
	/**
	 * Class name
	 * @access private
	 * @var string
	 */
	private $className = 'IndexController';


	/**
	 * Constructor
	 * @access public
	 */
	public function __construct()
	{
		// Nothing to do :p .. probably I will use it later, maybe not
		// Cheesus!!!, need other coffee cup
	}
	
	/**
	 * Index action
	 * @access public
	 * @return array (Will be converted in JSON response)
	 */
	public function index() 
	{
		return array(
			'message' => 'Reddit API (Demo) | ' . $this->randomQuote(), 
			'timestamp' => time(),
		);
	}
	
	/**
	 * Version action
	 * @todo Set version API in a 
	 * @access public
	 * @return array (Will be converted in JSON response)
	 */
	public function version() 
	{
		return array(
			'version' => $this->getDI()->get('config')->app->version, 
			'timestamp' => time(),
		);
	}

	/**
	 * Echo server
	 * @return array HTTP body (Will be converted in JSON response)
	 */
	public function mirror(){
		return $this->getDI()->get('requestBody');
	}

	/**
	 * Return random quote
	 * @return string Random quote
	 */
	protected function randomQuote() {
		$quotes = array(
			"Train yourself to let go of everything you fear to lose.",
			"Fear is the path to the dark side. Fear leads to anger. Anger leads to hate. Hate leads to suffering.",
			"Always pass on what you have learned.",
			"[Luke:] I can’t believe it. [Yoda:] That is why you fail.",
			"Powerful you have become, the dark side I sense in you.",
			"PATIENCE YOU MUST HAVE my young padawan",
			"Already know you that which you need. – Yoda",
			"Feel the force!",
			"To answer power with power, the Jedi way this is not. In this war, a danger there is, of losing who we are",
			"Nuclear war can ruin your whole compile.",
			"We all know Linux is great, it does infinite loops in 5 seconds." ,
			"I would love to change the world, but they won't give me the source code.",
			"My software never has bugs. It just develops random features.",
			"Windows isn't a virus, viruses do something.",
			"If Python is executable pseudocode, then perl is executable line noise.",
			"Debugging is twice as hard as writing the code in the first place. Therefore, if you write the code as cleverly as possible, you are, by definition, not smart enough to debug it.",
			"Always code as if the guy who ends up maintaining your code will be a violent psychopath who knows where you live.",
			"In theory, there is no difference between theory and practice. But, in practice, there is."
		);
		return $quotes[array_rand($quotes)];
	}
}
