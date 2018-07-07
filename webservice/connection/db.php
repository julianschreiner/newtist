<?php

class db{
	private $username;
  	private $password;
  	protected $link;

	public function __construct(){
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

	public function getLink(){
		return $this->link;
	}

	public function getUsername(){
		return $this->username;
	}
}