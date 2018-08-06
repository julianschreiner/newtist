<?php
require_once('../webservice/connection/db.php');

$dbCon = new db;
$dbCon = $dbCon->getLink();

// GET COLUMN GENRE

$handle = $dbCon->prepare("SELECT genre FROM artist_gd");
$handle->execute();
$result = $handle->fetchAll(\PDO::FETCH_ASSOC);

$counter = 0;
$bucket = [];
$pop = [];

foreach($result as $entry){
	//var_dump($entry['genre']);
	if(!empty($entry['genre'])){
		// SPLIT STRING BY COMMA
		$genres = explode(',', $entry['genre']);
		
		foreach($genres as $genre){
			// ADD TO BUCKET IF NON EXISTING
			
			if($genre == " hip hop" || $genre == " rap"){
				$genre = substr($genre, 1);
			}

			if(!in_array($genre, $bucket['description'])){
				$bucket['description'][] = $genre;
			}
			else{
				// TODO COUNT UP
				
			}
			
		}
	}
	$counter++;
}	//outter foreach

var_dump($bucket);

function findKeyOfValue($genre, $arr){
	$key = 0;
	if(empty($arr)) return $key;
	else{
		foreach($arr as $element){
			if($element['description'] == $genre){
				
			}
		}
	}

	return $key;
}