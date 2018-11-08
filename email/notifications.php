<?php
/**
 * Created by PhpStorm.
 * User: Julian
 * Date: 12.07.2018
 * Time: 20:42
 * Grab emails and call sendmail script
 */
define('MAXLIMIT', 50);
define('OFFSET', 0);

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'SendMail.php';
require_once '../webservice/connection/db.php';

$mailer = new SendMail();
$link = new db();

$link = $link->getLink();


function status_rep($status){
	echo $status . PHP_EOL;
}

function debug ($mystring){
	echo "<pre>" . print_r($mystring, true) . "</pre>";
}

function apiConnect($link){
	$url = 'https://accounts.spotify.com/api/token';

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

	return $headers;
}

function getNewReleases($header, $artistName){
	$requrl = "https://api.spotify.com/v1/browse/new-releases?limit=" . MAXLIMIT . "&offset=" . OFFSET;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $requrl);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = json_decode(curl_exec($ch), true);
	curl_close($ch);

	$retData = $response['albums']['items'];

	$artistData = [];


	foreach($retData as $key => $value){
		if($value['artists'][0]['name'] == $artistName){
			$artistData['artistURL'] = $value['artists'][0]['external_urls']['spotify'];
			$artistData['name'] = $value['name'];
			$artistData['release_date'] = $value['release_date'];
			$artistData['image'] = $value['images'][1]['url'];
			$artistData['allArtists'] = $value['artists'];
			$artistData['type'] = $value['album_type'];
			$artistData['uri'] = $value['external_urls']['spotify'];
		}
	}

	return $artistData;
}


if(isset($link) && isset($mailer)){
	$handle = $link->prepare('SELECT * FROM sub_handler');
	$handle->execute();

	$result = $handle->fetchAll(\PDO::FETCH_ASSOC);
	//debug($result);

	foreach($result as $r){
		$success = false;
		$user = $r['uid'];
		$artist = $r['artist'];

		$handle = $link->prepare('SELECT * FROM users WHERE username=:usr');
		$handle->bindValue(':usr', $user);

		$handle->execute();
		$usernameDB = $handle->fetchAll(\PDO::FETCH_ASSOC);

		if(isset($usernameDB[0])){
			$email = $usernameDB[0]['email'];
			//debug($email);

			/* CALL SPOTIFY API TO SEE IF ARTIST HAS DROPPED SOMETHING NEW --> CALL NEW RELEASES*/
			$header = apiConnect($link);
			$adata = getNewReleases($header, $artist);

			// TODO 
			if($artist == 'Quavo'){
				//var_dump($header);
				//var_dump($adata);
				//exit;
			}

			if(empty($adata)){
				continue;
			}else{
				$success = $mailer->send($email, $adata, $user);

				if($success){
					status_rep('Mail successfully sent to ' . $user . ' with email ' . $email . '<br>');
				}
				else{
					status_rep('Mail failed to send to ' . $user . ' with email ' . $email . '<br>');
				}
			
			}
		}




	}
}