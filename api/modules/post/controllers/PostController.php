<?php
/**
 * Post controller, manage posts actions
 *
 * @package    Reddit_API
 * @subpackage Post_Controllers
 * @author     Enrique Ojeda <enriqueojedalara@gmail.com>
 */
namespace Modules\Post\Controllers;

use Phalcon\Filter;
use Modules\Core\Controllers\BaseController;
use Modules\Post\Models\Posts;
use Modules\Post\Models\Votes;


class PostController extends BaseController
{
	/** 
	* Constructor
	*/
	public function __construct() 
	{
		parent::__construct();
	}

	/** 
	* Submit action (Create a new post)
	*
	* @return true 
	* @throws \Modules\Core\Exceptions\HTTPException
	*/
	public function submit() 
	{
		//Check if user is authorized
		\Modules\Core\Library\Authorize::isAuthorized();

		$post = new Posts;
		$vote = new Votes;
		$filter = new Filter;

		$this->validateRequireFields(array('title', 'url'));

		$filter->add('url', function ($value) {
			return preg_replace('/[^0-9a-zA-Z:\/.#_-]/', '', $value);
		});

		$params = array(
			'id' => $post->caldulateId(),
			'uid' => pack('H*', \Modules\Core\Library\Authorize::getUid()),
			'title' => $filter->sanitize($this->getDI()->get('requestBody')->title, 'string'),
			'url' => $filter->sanitize($this->getDI()->get('requestBody')->url, 'url'),
		);

		$post->save($params);

		return true;
	}

	/** 
	* Fetch action (Get a list of posts by page)
	*
	* @param int $page  Each page is set to 20 records (Defined in Model)
	* @throws \Modules\Core\Exceptions\HTTPException
	* @return array(Modules\Post\Models\Posts)
	*/
	public function fetch($page = 1) 
	{
		$post = new Posts;
		return $post->fetch((int)$page);
	}

	/** 
	* Vote action (Vote a post action)
	*
	* @throws \Modules\Core\Exceptions\HTTPException
	* @return int Votes after the vote
	*/
	public function vote()
	{
		//Check if user is authorized
		\Modules\Core\Library\Authorize::isAuthorized();

		$post = new Posts;
		$vote = new Votes;
		$filter = new Filter;

		$filter->add('vote', function ($value) {
			if ($value > 0) return 1;
			if ($value < 0) return -1;
			return 0;
		});

		$params = array(
			'id' =>  $this->getDI()->get('requestBody')->id,
			'uid' => \Modules\Core\Library\Authorize::getUid(),
			'vote' => $filter->sanitize((int)$this->getDI()->get('requestBody')->vote, 'vote'),
		);

		$userVote = Votes::find("id = 0x".$params['id']." AND uid = 0x".$params['uid']);
		
		$oldVote = 0;
		if (isset($userVote->getFirst()->vote)){
			$oldVote = $userVote->getFirst()->vote;
		}
		
		if ((int)$oldVote == (int)$params['vote']){
			$params['vote'] = 0;
		}

		$vote->save($params);

		return $post->getVotes($this->getDI()->get('requestBody')->id);
	}
}