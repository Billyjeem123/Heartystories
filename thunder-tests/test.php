<?php

$data = file_get_contents('php://input');
$data = json_decode($data);

$files =  $data->files;
// var_dump($data);
// // echo $files['co'];
// exit;
// // var_dump($files);
// // exit;



foreach ($files as $key => $value) {

    // echo $value['co'];
echo ($value->deed_Ass);
    
    # code..
}