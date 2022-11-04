<?php

/**
 *
 */
class Unknown extends db
{




    // output data
  public function outputData($success = null, $message = null, $data = null)
  {
    $arr_output = array(
      'success' => $success,
      'message' => $message,
      'data' => $data,
    );
    echo json_encode($arr_output);
  }

}
