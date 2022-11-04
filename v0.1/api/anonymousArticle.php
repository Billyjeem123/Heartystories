<?php
header('Content-Type: application/json charset=utf-8');
header("Access-Control-Allow-Methods: PUT, GET, POST");

require('../assets/config.php');

if (!empty($_POST['from_id']) and !empty($_POST['to_id']) and $_POST['article'])
{

    $posts = new Posts();


    if($posts->createPost2($_POST['from_id'], $_POST['to_id'], $_POST['article'])){exit();}

}else{
    
   $Utility = new Utility;

   $Utility->outputData(false, 'Invalid parameter', null);
   exit;

}

?>