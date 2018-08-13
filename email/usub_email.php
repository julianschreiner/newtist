<?php
/**
 * Created by PhpStorm.
 * User: Julian
 * Date: 06.08.2018
 * Time: 13:52
 */

require_once '../webservice/connection/db.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// TODO MAKE IT SECURE with param hashing md5 + timestamp !!!

if(isset($_GET['artist']) && isset($_GET['user'])){
	$link = new db();
	$link = $link->getLink();

	if(isset($link)){
		$handle = $link->prepare('DELETE FROM sub_handler WHERE uid=:usr AND artist=:art');
		$handle->bindValue(':usr', $_GET['user']);
		$handle->bindValue(':art', $_GET['artist']);
		$handle->execute();

		echo $_GET['user'] . ' you have been successfully unsubscribed from the artist ' . $_GET['artist'];
		exit;
	}
}