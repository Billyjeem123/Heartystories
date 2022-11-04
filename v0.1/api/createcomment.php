<?php
header('Content-Type: application/json charset=utf-8');
header("Access-Control-Allow-Methods: PUT, GET, POST");

require('../assets/config.php');

$Utility = new Utility();

//  isset($_POST['articleid'], $_POST['commentid'], $_POST['userid']):  ? $Utility->validateParams();

if (!empty($_POST['postToken']) and !empty($_POST['comment']) and !empty($_POST['userid']))
{

    $posts = new Posts();


    if($posts->createComment($_POST['postToken'], $_POST['comment'], $_POST['userid'])){exit();}

}else{
    $array = ["success" => true, "message" => "Invalid parameter", 'data' => null];
    $return = json_encode($array);
    echo "$return";
    exit();
}

?>