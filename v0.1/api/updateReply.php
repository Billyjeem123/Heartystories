<?php
header('Content-Type: application/json charset=utf-8');
header("Access-Control-Allow-Methods: PUT, GET, POST");

require('../assets/config.php');

if (!empty($_POST['reply']) and !empty($_POST['userid']) and !empty($_POST['replyToken']))
{

    $posts = new Posts();


    if($posts->updateComment($_POST['userid'], $_POST['reply'], $_POST['replyToken'])){exit();}

}else{

    $array = ["success" => true, "message" => "Invalid parameter", 'data' => null];
        $return = json_encode($array);
        echo "$return";
        exit();
}

?>