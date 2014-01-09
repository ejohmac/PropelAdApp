<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2013, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\controllers;

use app\models\FacebookFriends;

class DownloadController extends \lithium\action\Controller {

	private $logfile = '/var/www/html/lithium/framework/app/log.txt';

	protected function _init() { 
		// the negotiate option tells li3 to serve up the proper content type 
		$this->_render['negotiate'] = true; 
		parent::_init(); 
	}

	public function index() {

		$postData = $this->request->data;
		//file_put_contents($this->logfile, print_r($postData,true),FILE_APPEND);

		$userIDArr = array_keys($postData);
		$userID = array_pop($userIDArr);

		// first check if this users friends are already in DB
		$result = $this->checkDB($userID);

		if ($result->count() != 0) {
			return (json_encode(array("msg" => "Your Friends are already in the DB")));
		} else {

			$insertArray = array();

			foreach ($postData as $valueArray) {
				foreach ($valueArray as $key => $value) {
					$insertArray[] = $value['name'];
				}
			}

			$model = FacebookFriends::create();
			$model->userid = $userID;
			$model->friends = $insertArray;
			$model->save();
		
			return (json_encode(array("msg" => count($insertArray)." Friends Downloaded to Database")));
		}
	
	}

	public function retreive() {

		$userID = (int)$this->request->query['id'];
		
		// check if this users friends are already in DB
		$result = $this->checkDB($userID);

		if ($result->count() != 0) {

			// Convert to array
			$doc = $result->to('array');

			$docArray = array_pop($doc);
			$friendsArray = $docArray['friends'];
			
			// Delete friends and delete
			FacebookFriends::remove(array('userid' => $userID));

			return (json_encode(array("msg" => "Here are your Friends", "data" => $friendsArray)));
		} else {
			return (json_encode(array("msg" => "You have No Friends in Database")));
		}

	}

	private function checkDB($userID) {

		// first check if this users friends are already in DB
		return FacebookFriends::find('all', array('conditions' => array('userid' => $userID)));

	}

	public function to_string() {
		return "Hello World";
	}

	public function to_json() {
		return $this->render(array('json' => 'Hello World'));
	}
}

?>
