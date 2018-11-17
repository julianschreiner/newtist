<?php
require_once('connection/db.php');


header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
//header('Content-Type: application/json');

switch($_GET['f']) {
	case 'route':
		$fRouting = new Frouter;
		$str_json = file_get_contents('php://input');
		if(!empty($str_json) && !is_null($str_json)){
			$fRouting->route($str_json);
		}
}


class Frouter {
	protected $link;
	public function __construct(){
		//make db connection
		$database = new db;
		$this->link = $database->getLink();
	}

	/**
	 * Redirects to function depends on JSON sent
	 *
	 * @param    array  $pms apikey, method
	 * @return   void
	 *
	 */
	public function route($pms){
		$parsedJSON = json_decode($pms, true);
		$object = $parsedJSON['obj']['frouter'];
		$method = $object['method'];
		//echo json_encode($method);

		switch($method){
			case 'genre':
				$this->getGenreData();
				break;
			case 'subscribe':
				$userID = (isset($object['userID']) ? $object['userID'] : '');
				$artistName = $object['artistName'];
				$this->subscribe($userID, $artistName);
				break;
			case 'isSub':
				$userID = (isset($object['userID']) ? $object['userID'] : '');
				$artistName = $object['artistName'];
				$this->isSub($userID, $artistName);
				break;
			case 'unsubscribe':
				$userID = (isset($object['userID']) ? $object['userID'] : '');
				$artistName = $object['artistName'];
				$this->unsubscribe($userID, $artistName);
				break;
			case 'notificationHandle':
				$userID = (isset($object['userID']) ? $object['userID'] : '');
				$this->notificationHandle($userID);
				break;
			case 'serviceWorker':
				$userID = (isset($object['userID']) ? $object['userID'] : '');
				$this->callServiceWorker();
				break;
			default:
				echo json_encode("API ERR");
				break;
		}
	}

	/**
	 * Helper Function for getting Data from DB
	 *
	 * @param    void
	 * @return   string json
	 *
	 */
	private function getGenreData(){
		$handle = $this->link->prepare("SELECT name, genre FROM artist_gd");

		$handle->execute();

		$result = $handle->fetchAll(\PDO::FETCH_ASSOC);

		echo json_encode($result);
	}

	/**
	 * Function to subscribe to an artist and get status updates via mail
	 *
	 * @param    $userID      string
	 * @param    $artistName  string
	 * @return   boolean
	 *
	 */
	private function subscribe($userID, $artistName){
		$success = false;
		$retArray = [];

		$handle = $this->link->prepare('SELECT * FROM sub_handler WHERE uid = :userID AND artist = :artistName');
		$handle->bindValue(':userID', $userID);
		$handle->bindValue(':artistName', $artistName);

		$handle->execute();

		$rowCount = $handle->rowCount();

		if($rowCount == 0){
			$handle = $this->link->prepare('INSERT INTO sub_handler (uid, artist)  VALUES (:userID, :artistName)');
			$handle->bindValue(':userID', $userID);
			$handle->bindValue(':artistName', $artistName);

			$handle->execute();

			$success = true;
			$retArray[] = $success;
		}
		else{
			//  THROW ERROR
			$retArray[] = $success;
		}

		echo json_encode($retArray);

		return $success;
	}

	/**
	 * @param $userID        string
	 * @param $artistName    string
	 * @return boolean
	 */
	private function unsubscribe($userID, $artistName){
		$success = false;
		$retArray = [];

		$handle = $this->link->prepare('SELECT * FROM sub_handler WHERE uid = :userID AND artist = :artistName');
		$handle->bindValue(':userID', $userID);
		$handle->bindValue(':artistName', $artistName);

		$handle->execute();

		$rowCount = $handle->rowCount();

		if($rowCount > 0){
			$handle = $this->link->prepare('DELETE FROM sub_handler WHERE uid = :userID AND artist = :artistName');
			$handle->bindValue(':userID', $userID);
			$handle->bindValue(':artistName', $artistName);

			$handle->execute();

			$success = true;
			$retArray[] = $success;
		}
		else{
			$success = false;
			$retArray[] = $success;
		}

		echo json_encode($retArray);

		return $success;
	}

	/**
	 * @param $userID        string
	 * @param $artistName    string
	 * @return boolean
	 */
	private function isSub($userID, $artistName){
		$success = false;
		$retArray = [];

		$handle = $this->link->prepare('SELECT * FROM sub_handler WHERE uid = :userID AND artist = :artistName');
		$handle->bindValue(':userID', $userID);
		$handle->bindValue(':artistName', $artistName);

		$handle->execute();

		$rowCount = $handle->rowCount();

		if($rowCount > 0){
			$success = true;
			$retArray[] = $success;
		}
		else{
			$success = false;
			$retArray[] = $success;
		}

		echo json_encode($retArray);

		return $success;
	}

	/**
	 * @param $userID        string
	 * @return boolean
	 */
	private function notificationHandle($userID){
		$success = false;
		$retArray = [];


		$handle = $this->link->prepare('SELECT * FROM sub_handler WHERE uid=:userID');
		$handle->bindValue(':userID', $userID);
		$handle->execute();

		$result = $handle->fetchAll(\PDO::FETCH_ASSOC);
		//debug($result);

		foreach($result as $res){
			$retArray[] = $res['artist'];
		}

		echo json_encode($retArray);

		return $success;
	}

	/**
	 * @return boolean
	 */
	private function callServiceWorker(){
		// JUST CALL IT ONCE A WEEK
		$date = date("m.d.y");
		

		$handle = $this->link->prepare('SELECT done FROM mail_log WHERE job_date =:myDate');
		$handle->bindValue(':myDate', $date);
		$handle->execute();

		$result = $handle->fetchAll(\PDO::FETCH_COLUMN);

		if(empty($result) || $result == 0){
			$handle = $this->link->prepare('INSERT INTO mail_log (job_date, done)  VALUES (:myDate, :done)');
			$handle->bindValue(':myDate', $date);
			$handle->bindValue(':done', 0);
			$handle->execute();

			include('../email/notifications.php');
		}
		else{
			echo "script ran already";
		}
	}

}
