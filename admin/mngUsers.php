<?php
/**
 * Created by PhpStorm.
 * User: Julian
 * Date: 12.08.2018
 * Time: 14:16
 */

require('../webservice/connection/db.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);

$db = new db;
$db = $db->getLink();


function getData($db){
	$handle = $db->prepare('SELECT id, username, email FROM users ORDER by id ASC');
	$handle->execute();
	$res = $handle->fetchAll(\PDO::FETCH_ASSOC);

	return $res;
}

$res = getData($db);

//echo "<pre>" . print_r($res, true) . "</pre>";

if(isset($_GET['delid'])){
	$handle = $db->prepare('DELETE FROM users WHERE id=:id');
	$handle->bindParam('id', $_GET['delid']);
	$handle->execute();

	$res = getData($db);
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<title>Newtist</title>
</head>
<body>
<h3 class="flow-text">Users</h3>
<a href="/"><i class="material-icons">backspace</i></a>
<table>
	<thead>
	<tr>
		<th>ID</th>
		<th>Username</th>
		<th>Email</th>
		<th>Delete</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach($res as $results): ?>
		<tr>
			<td><?php echo $results['id'] ?></td>
			<td><?php echo $results['username'] ?></td>
			<td><?php echo $results['email'] ?></td>
			<td><a href="?delid=<?php echo $results['id'] ?>"><i class="material-icons">delete_outline</i></a></td>
		</tr>
	<?php endforeach; ?>

	</tbody>
</table>



</body>
</html>