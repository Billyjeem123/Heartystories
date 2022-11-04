<?php
header('Content-Type: application/json charset=utf-8');
header("Access-Control-Allow-Methods: PUT, GET, POST");

require('../assets/config.php');

if (!empty($_POST['to_id']))
{

    $posts = new Posts();

    $Utility = new Utility();

        $fetchCmt = $posts->fetchPostsMessage($_POST['to_id']);
        if ($fetchCmt !== false) {
            // code...
            echo $Utility->outputData(true, "Fetch Details", $fetchCmt);
            exit();
        } else {
            echo $Utility->outputData(false, 'Unable to process', null);
            exit();
        }

}else{
    $array = ["success" => true, "message" => "Invalid parameter", 'data' => null];
        $return = json_encode($array);
        echo "$return";
        exit();

}