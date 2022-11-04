<?php
header('Content-Type: application/json charset=utf-8');
header("Access-Control-Allow-Methods: PUT, GET, POST");

require('../assets/config.php');

$Utility = new Utility();
if (!empty($_POST['userid'])) {

    $posts = new Posts();


    $fetchMergedPosts = $posts->fetchMergedPosts($_POST['userid']);

    $fetchMergedMsg = $posts->fetchMergedMsg($_POST['userid']);

    $merge_method  = array_merge($fetchMergedPosts, $fetchMergedMsg);


    if ($merge_method !== false) {
        // code...
        echo $Utility->outputData(true, "Fetch Details", $merge_method);
        exit();
    } else {
        echo $Utility->outputData(false, 'Unable to process', null);
        exit();
    }
} else {
    $array = ["success" => true, "message" => "Invalid parameter", 'data' => null];
    $return = json_encode($array);
    echo "$return";
    exit();
}
