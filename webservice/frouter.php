<?php
require_once('connection/db.php');

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
