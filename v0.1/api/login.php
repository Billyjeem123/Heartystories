<?php
header('Content-Type: application/json charset=utf-8');
header("Access-Control-Allow-Methods: PUT, GET, POST");

require('../assets/config.php');


$Utility = new Utility();


$User = new Users();
$login = $User->tryLogin($_POST['username'], $_POST['password']);
if ($login !== false) {
	// code...
	echo $Utility->outputData(true, "Login successful..", $login);
	exit();
} else {
	echo $Utility->outputData(false, $_SESSION['err'], null);
	exit();
}
