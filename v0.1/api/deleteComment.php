<?php
header('Content-Type: application/json charset=utf-8');
header("Access-Control-Allow-Methods: PUT, GET, POST");

require('../assets/config.php');

$Utility = new Utility();
if (isset($_POST['commentToken']) and !empty($_POST['userid'])) {

    $posts = new Posts();


    if($posts->deleteComment($_POST['commentToken'], $_POST['userid'])){exit;}

}else{

       $array = ["success" => true, "message" => "Invalid parameter", 'data' => null];
        $return = json_encode($array);
        echo "$return";
        exit();
}

?>