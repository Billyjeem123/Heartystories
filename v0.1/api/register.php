<?php
header('Content-Type: application/json charset=utf-8');
header("Access-Control-Allow-Methods: PUT, GET, POST");

require('../assets/config.php');

if(isset($_POST['username'])){

	$username = ($_POST['username']);
	$password = ($_POST['password']);

	$user = new Users();

	if($user->createAccount($username, $password)){exit;}

}

?>