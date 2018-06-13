<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header('Content-Type: application/json');

switch($_GET['f']) {
case 'route':
  $fRouting = new Frouter;
  $str_json = file_get_contents('php://input');
  if(!empty($str_json) && !is_null($str_json)){
    $fRouting->route($str_json);
  }

}


class Frouter {
  private $username;
  private $password;
  protected $link;

  public function __construct(){
    //maybe db
    $credFile = fopen("../creds.ini", "r") or die("Unable to open file!");
    $creds = fread($credFile,filesize("../creds.ini"));

    $this->username = strtok($creds, ':');

    $password = strtok('');
    $this->password = preg_replace('/\v(?:[\v\h]+)/', '', $password);

    fclose($credFile);

    /*DB LINK*/
    $this->link = new \PDO(   'mysql:host=rlated12.lima-db.de;dbname=db_363124_3;charset=utf8mb4',
    (string)$this->username,
    (string)$this->password,
    array(
      \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
      \PDO::ATTR_PERSISTENT => false
    )
    );

  }

  /**
  * Redirects to function depends on JSON sent
  *
  * @param    array  $pms apikey, method
  * @return      nothing
  *
  */
  public function route($pms){
    $parsedJSON = json_decode($pms, true);
    $method = $parsedJSON['obj']['frouter']['method'];
    //echo json_encode($method);

    switch($method){
      case 'genre':
          $this->getGenreData();
    }
  }

  /**
  * Helper Function for getting Data from DB
  *
  * @param    nothing
  * @return   json with artist name + genre
  *
  */
  private function getGenreData(){
    $handle = $this->link->prepare("SELECT name, genre FROM artist_gd");

    $handle->execute();

    $result = $handle->fetchAll(\PDO::FETCH_ASSOC);

    echo json_encode($result);
  }

}
