<?php
require('../webservice/connection/db.php');


class Auth{
   protected $link;
   private $username;
   private $password;
   private $email;

   /**
   * Template
   *
   * @param    array  $pms apikey, method
   * @return      nothing
   *
   */

	public function __construct(){
		//make db connection
		$database = new db;
		$this->link = $database->getLink();
	}

	public function login($username, $password){
		$okay = false;
		$password = md5($password);
			
		//LOOK IF EMAIL IS ALREADY USED OR USERNAME
		$handle = $this->link->prepare('SELECT * FROM users WHERE username = :username and password = :password');
		
		$handle->bindValue(':username', $username, PDO::PARAM_STR);
		$handle->bindValue(':password', $password, PDO::PARAM_STR);
		$handle->execute();
		
		$result = $handle->fetchAll(\PDO::FETCH_OBJ);
		
		if(!empty($result)){
        	session_start();
        	$_SESSION['id'] = $username;

        	$handle = $this->link->prepare("
				UPDATE users set last_login = :timestamp WHERE username = :usuername
			");

			$handle->bindValue(":username", $username);
        	$handle->bindValue(":timestampp", date('Y-m-d H:i:s','1299762201428'));
        	$handle->execute();

        	
        	header("Location: ../index.php?reg=success");
        	$okay = true;
		}
		else{
			$okay = false;
		}

		return $okay;
	}
	
	public function register($username, $password, $email){
		$okay = false;
		$password = md5($password);
		$email = filter_var($email, FILTER_VALIDATE_EMAIL);
		
		//LOOK IF EMAIL IS ALREADY USED OR USERNAME
		$handle = $this->link->prepare('SELECT * FROM users WHERE username = :username or email = :email');
		
		$handle->bindValue(':username', $username, PDO::PARAM_STR);
		$handle->bindValue(':email', $email, PDO::PARAM_STR);
		$handle->execute();
		
		$result = $handle->fetchAll(\PDO::FETCH_OBJ);
		
		if(empty($result)){
			$handle = $this->link->prepare("
				INSERT INTO users (username, password, email, timestamp_created) VALUES (:username, :password, :email, :timestampp)"
			);

        	$handle->bindValue(":username", $username);
        	$handle->bindValue(":password", $password);
        	$handle->bindValue(":email", $email);
        	$handle->bindValue(":timestampp", date('Y-m-d H:i:s','1299762201428'));
        	$handle->execute();

        	session_start();
        	$_SESSION['id'] = $username;

        	header("Location: ../index.php?reg=success");
        	$okay = true;
		}
		else{
			$okay = false;
		}

		return $okay;

	}

	public function logout(){
		session_start();
		session_destroy();

    	header("Location: ../index.php?reg=logdout");
    	exit;
	}
}
