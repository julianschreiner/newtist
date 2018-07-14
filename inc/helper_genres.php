<?php
require_once('../webservice/connection/db.php');
$offsetnmbr = 0;
define('MAXLIMIT', 50);
define('OFFSET', $offsetnmbr);

$link = new db;
$link = $link->getLink();

$url = 'https://accounts.spotify.com/api/token';

function status_rep($status){
  echo $status . PHP_EOL;
}

status_rep("running.....");


if(isset($_GET['offset'])){
  if($_GET['offset'] > 0 && $_GET['offset'] < 500) $offsetnmbr = $_GET['offset'];
}

if(isset($argv[1]) && !empty($argv[1])){
  if($argv[1] > 0 && $argv[1] < 500) $offsetnmbr = $argv[1];
}

status_rep("Set offset: " . $offsetnmbr);

if(isset($argv[1]) && $argv[1] == 'count'){
  $handle = $link->prepare('SELECT count(*) FROM artist_gd');

  $handle->execute();

  $num_of_rows = $handle->fetchColumn();

  status_rep("Total Entries: " . $num_of_rows);
  exit;
}

//$handle = $link->prepare('SELECT * FROM artist_gd WHERE name = :sample');
//$handle->bindValue(":sample", $argv[1], PDO::PARAM_STR);
//$handle->execute();
//$result = $handle->fetchAll(\PDO::FETCH_OBJ);

$handle = $link->prepare('SELECT * FROM spotify_cred WHERE id = ?');

$handle->bindValue(1, 1, PDO::PARAM_INT);

$handle->execute();

$result = $handle->fetchAll(\PDO::FETCH_OBJ);

$client_id = $result[0]->cl_id;
$client_secret = $result[0]->cl_sec;

$credentials = "{$client_id}:{$client_secret}";


$headers = array(
  "Accept: */*",
  "Content-Type: application/x-www-form-urlencoded",
  "User-Agent: runscope/0.1",
  "Authorization: Basic " . base64_encode($credentials));

  $data = 'grant_type=client_credentials';


  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = json_decode(curl_exec($ch), true);
  curl_close($ch);

  // var_dump($response['access_token']);
  $acToken = $response['access_token'];
  status_rep("Got Access Token.....");


  $headers =
  array(
    "Accept: */*",
    "Content-Type: application/x-www-form-urlencoded",
    "User-Agent: runscope/0.1",
    "Authorization: Bearer " . $acToken
  );

  //NEW RELEASES REQUEST
  //$maxLimit = 50;
  //$offset = 0;
  $requrl = "https://api.spotify.com/v1/browse/new-releases?limit=" . MAXLIMIT . "&offset=" . OFFSET;

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $requrl);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = json_decode(curl_exec($ch), true);
  curl_close($ch);

  $retData = $response['albums']['items'];
  $artistNames = [];

  foreach($retData as $key => $value){
    $artistNames[] = $value['artists'][0]['name'];
  }

  status_rep("Got New Releases Artist Names.....");

  /* SAVE ARTIST */
  foreach($artistNames as $name){
      /*LOOK IF ITS IN THERE ALREADY*/
      $handle = $link->prepare("SELECT * FROM artist_gd WHERE name = :name");

      $handle->bindValue(":name", $name);

      $handle->execute();

      $result = $handle->fetchAll(\PDO::FETCH_OBJ);

      if(empty($result)){
        $handle = $link->prepare("INSERT INTO artist_gd (name) VALUES (:names)");

        $handle->bindValue(":names", $name);

        $handle->execute();
      }
  }

  status_rep("Saved Artist Names.....");
  $updateCounter = 0;
  //echo "<pre>". print_r($artistNames, true) . "</pre>";

  foreach($artistNames as $name){
    $requrl = "https://api.spotify.com/v1/search?q=". urlencode($name) ."&type=artist&market=DE&limit=1";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $requrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    $artistItems = $response['artists']['items'];
    $allGenres = '';

    /*LOOK IF GENRE IS SET ALREADY ALREADY*/
    $handle = $link->prepare("SELECT * FROM artist_gd WHERE name = :name");

    $handle->bindValue(":name", $name);

    $handle->execute();

    $result = $handle->fetchAll(\PDO::FETCH_ASSOC);

    $skip = (empty($result[0]['genre']) ? false : true);

    if($skip == false){
      foreach($artistItems as $genres){

        $allGenres = implode(",", $genres['genres']);

        if(!empty($allGenres)){
          $updateCounter++;
        }

        $handle = $link->prepare("UPDATE artist_gd SET genre = :genre WHERE name = :name");

        $handle->bindValue(":genre", $allGenres);
        $handle->bindValue(":name", $name);

        $handle->execute();
      }
    }
  }

  status_rep("Updated " . $updateCounter . " entries");



  /* GET NEW RELEASES -> SAVE ARTIST NAMES -> IF NOT IN DB ALREADY -> SAVE GENRE */
