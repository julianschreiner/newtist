<?php
include 'classes/auth.php';
$auth = new Auth;
session_start();

if(isset($_POST['action']) && !empty($_POST)){
	$username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    $success = $auth->register($username, $password, $email);

    
   
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
	<title>Newtist - Register</title>
</head>
<body class="container">
	 <div class="row">
         <form class="col s12 m6 offset-m4" method="POST">
                    <div class="row">
                        <?php if(isset($_POST['action']) && $success == false) : ?>
                            <h6 style="color:red;">User already exists.</h6>
                        <?php endif;?>
                    </div>
         			<div class="row">
         				<div class="input-field col s6">
         					<i class="material-icons prefix">account_circle</i>
         					<input id="icon_prefix" type="text" class="validate" required data-length="15" name="username">
         					<label for="icon_prefix">Username</label>
         				</div>
         			</div>
         			<div class="row">
         				<div class="input-field col s6">
         					<i class="material-icons prefix">security</i>
         					<input id="icon_password" type="password" class="validate" required name="password">
         					<label for="icon_password">Password</label>
         				</div>
         			</div>
         			<div class="row">
         				<div class="input-field col s6">
         					<i class="material-icons prefix">email</i>
         					<input id="icon_email" type="email" class="validate" required name="email">
         					<label for="icon_email">Email</label>
         				</div>
         			</div>
         			<div class="row center-align">
         				<button class="btn waves-effect waves-light" type="submit" name="action">Register
         					<i class="material-icons right">arrow_right</i>
         				</button>
         			</div>   
         </form>       
      </div>
</body>
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
		
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">

    <script src="../js/ui.js"></script>

    <link rel="stylesheet" href="../css/main.scss">

    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</html>