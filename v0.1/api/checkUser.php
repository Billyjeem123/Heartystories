<?php
header('Content-Type: application/json charset=utf-8');
header("Access-Control-Allow-Methods: PUT, GET, POST");

require('../assets/config.php');

if (!empty($_POST['username']))
{

    $user = new Users();

    $Utility = new Utility();

        if($user->checkMateUser($_POST['username'])){exit;}
        

}else{
    $array = ["success" => true, "message" => "Invalid parameter", 'data' => null];
    $return = json_encode($array);
    echo "$return";
    exit();

}
