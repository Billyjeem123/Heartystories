<?php

/**
 * 
 */
class Auth extends db
{
	
	// authenticate apptoken
	public function CheckToken($apptoken='')
	{
		// code...
		 try {
         $sql = "SELECT * FROM apptoken WHERE token = '$apptoken'";
                $stmt = $this->connect()->prepare($sql);
                if (!$stmt->execute()) {
                    $stmt = null;
                  $_SESSION['err'] = "Something went wrong, please try again..";
                    return false;
                }else{
if($stmt->rowCount() == 0){

           $stmt = null;
           $_SESSION['err'] = "No app found.";
            return false;
            // code...
        }else{
            
           if($biz = $stmt->fetchAll(PDO::FETCH_ASSOC)){

$posts_arr = array();

return true;
    }else{
    	return false;
    }

    }
}
    } catch (PDOException $e) {
        $_SESSION['err'] = $e->getMessage();
  return false;
    }
	}

    // / authenticate apptoken
public function AuthToken($usertoken = '')
{
    try{
    $sql = "SELECT * FROM users WHERE token = '$usertoken'";
    $stmt = $this->connect()->prepare($sql);
    if (!$stmt->execute()) {
        $stmt = null;
        $_SESSION['err'] = "Something went wrong, please try again..";
        return false;
    } else {
        if ($stmt->rowCount() == 0) {

            $stmt = null;
            $_SESSION['err'] = "Unrecognied user";
            return false;
            // code...
        } else {

            if ($stmt->fetchAll(PDO::FETCH_ASSOC)) {

                return true;
            } else {
                return false;
            }
        }
    }
}   catch (PDOException $e) {
    $this->outputData(false, $_SESSION['err'] = $e->getMessage(), null);
    return false;



}

}

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



?>