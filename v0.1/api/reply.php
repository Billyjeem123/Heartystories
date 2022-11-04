<?php
header('Content-Type: application/json charset=utf-8');
header("Access-Control-Allow-Methods: PUT, GET, POST");

require('../assets/config.php');

$Utility = new Utility();

//  isset($_POST['articleid'], $_POST['commentid'], $_POST['userid']):  ? $Utility->validateParams();

if (!empty($_POST['commentToken']) and !empty($_POST['reply']) and !empty($_POST['userid']) and !empty($_POST['postToken']))
{

    $posts = new Posts();


    if($posts->createReply($_POST['commentToken'], $_POST['reply'], $_POST['userid'], $_POST['postToken'])){exit();}

}else{


    $array = ["success" => true, "message" => "Invalid parameter", 'data' => null];
    $return = json_encode($array);
    echo "$return";
    exit();
}

?>