<?php
header('Content-Type: application/json charset=utf-8');
header("Access-Control-Allow-Methods: PUT, GET, POST");

require('../assets/config.php');

if (!empty($_POST['comment']) and !empty($_POST['userid']) and !empty($_POST['commentToken']))
{

    $posts = new Posts();


    if($posts->updateComment($_POST['userid'], $_POST['comment'], $_POST['commentToken'])){exit();}

}else{

    $array = ["success" => true, "message" => "Invalid parameter", 'data' => null];
        $return = json_encode($array);
        echo "$return";
        exit();
}

?>