<?php
header('Content-Type: application/json charset=utf-8');
header("Access-Control-Allow-Methods: PUT, GET, POST");

require('../assets/config.php');

$Utility = new Utility();
if (isset($_POST['postToken']) and !empty($_POST['userid'])) {

    $posts = new Posts();


    if($posts->deleleArticle($_POST['postToken'], $_POST['userid'], $_POST['type'])){exit;}

}else{

    $array = ["success" => true, "message" => "Invalid parameter", 'data' => null];
    $return = json_encode($array);
    echo "$return";
    exit();
}

?>