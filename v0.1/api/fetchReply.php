<?php
header('Content-Type: application/json charset=utf-8');
header("Access-Control-Allow-Methods: PUT, GET, POST");

require('../assets/config.php');

if (!empty($_POST['commentToken'])  and !empty($_POST['userid']))
{

    $posts = new Posts();

    $Utility = new Utility();

        $fetchCmt = $posts->fetchReply($_POST['commentToken'], $_POST['userid']);
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
