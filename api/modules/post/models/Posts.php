<?php
/**
 * Post Model, manage posts database actions
 *
 * @package    Reddit_API
 * @subpackage Post_Models
 * @author     Enrique Ojeda <enriqueojedalara@gmail.com>
 */
namespace Modules\Post\Models;

use Phalcon\DI as DI;
use Phalcon\DI\FactoryDefault;
use Phalcon\Filter;


class Posts extends \Phalcon\Mvc\Model {

	/**
	 * Total of rows to show in each page
	 * @const
	 * @todo Move this to the config file
	 */
	const LIMIT = 20;

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
	 * Votes
	 * @access protected
	 * @var int
	 */
	protected $votes;

	/**
	 * Status
	 * @access protected
	 * @var int
	 */
	protected $status;

	/**
	 * Title
	 * @access protected
	 * @var string
	 */
	protected $title;

	/**
	 * Url
	 * @access protected
	 * @var string
	 */
	protected $url;

	/**
	 * Creation
	 * @access protected
	 * @var int
	 */
	protected $creation;


	/** 
	 * Initialize model
	 */
	public function initialize() {
	    $this->setSource('posts');
        $this->skipAttributesOnUpdate(array('id'));
	}

	/** 
	 * Set validations after fetch values
	 */
	public function afterFetch() {
        $this->id = bin2hex($this->id);
    }

    /** 
	 * Caldulate ID, this is a helper because we use bynary(16) values in database
	 * More info see `Ordered UUID`
	 *
	 * @return string UUID generated value
	 */
    public function caldulateId() {
    	$sql = 'SELECT uuid_reddit(UUID()) AS id';
    	$rs = DI::getDefault()->get('db')->query($sql);
		$rs->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
		return $rs->fetch()['id'];
    }

    /** 
	 * Fetch posts by page (Each page is 20 records)
	 * 
	 * @param int $page Number of page to get records
	 * @return array(array(posts), array(votes by user))
	 */
	public function fetch($page) {
		$offset = $page * self::LIMIT - self::LIMIT; 

		$sql = 'SELECT @count := ifnull(@count, 0) + 1 as rank, '
			. ' HEX(id) as id, username, title, url, unix_timestamp(creation) as creation, votes, score'
			. ' FROM'
			. ' vv_posts p, (select @count := '.$offset.') c'
			. ' LIMIT '.$offset.', '.self::LIMIT;

		$rs = DI::getDefault()->get('db')->query($sql);
		$rs->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
		$posts = $rs->fetchAll();

		$ids = array();
		foreach($posts as $post){
			$ids[] = '0x'.$post['id'];
		}

		$userVotes = array();

		try {
			$uid = \Modules\Core\Library\Authorize::getUid();
		}
		catch(\Modules\Core\Exceptions\HTTPException $e){
			$uid = false;
		}

		if ($uid) {
			$sql = 'SELECT HEX(id) as id, vote FROM votes '
				. ' WHERE '
				. ' uid = 0x' . $uid . ' AND'
				. ' id IN ('.implode(',', $ids).')';
			$rs = DI::getDefault()->get('db')->query($sql);
			$rs->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
			$votes = $rs->fetchAll();
			
			
			foreach($votes as $vote){
				$userVotes[$vote['id']] = $vote['vote'];
			}
		}

		return array( 
			'posts' => $posts,
			'votes' => $userVotes
		);
	}

	/**
	 * Get total of votes from an specific post
	 *
	 * @param string Post id
	 * @return int Total of votes 
	 */
	public function getVotes($id = '') {
		$sql = 'SELECT votes FROM vv_posts WHERE id = 0x'.$id;
		$rs = DI::getDefault()->get('db')->query($sql);
		$rs->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
		return $rs->fetch()['votes'];
	}

	/**
	 * You should know what is this for ;)
	 */
	public function __get($name) {
        if (isset($this->$name)) {
            return $this->$name;
        }
        return null;
    }
}