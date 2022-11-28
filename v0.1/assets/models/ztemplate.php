<?php

/**
 *
 */
class Users extends db
{
    // check if user email exist
    public function userExists($mail = '')
    {

        try {
            $sql = "SELECT * from tblusers where username = '$mail'";
            $stmt = $this->connect()
                ->prepare($sql);
            if (!$stmt->execute()) {
                $stmt = null;
                $_SESSION['err'] = "Something went wrong, please try again..";
                return false;
            } else {
                if ($stmt->rowCount() == 0) {

                    $stmt = null;
                    $_SESSION['err'] = "No user found..";
                    return false;
                } else {

                    if ($user = $stmt->fetchAll(PDO::FETCH_ASSOC)) {
                        return true;
                    }
                }
            }
        } catch (PDOException $e) {
            $this->outputData(false, $_SESSION['err'] = $e->getMessage(), null);
            return false;
        }
    }

    /*
@param mixed $mail
@param mixed $pword
@return boolean

*/

  
public function createAccount($username, $pword)
{
    // code...
    try {

        global $mysqli;

        $escapeUsername = mysqli_real_escape_string($mysqli, $username);

        $hashPword = password_hash($pword, PASSWORD_DEFAULT);

        $escapePword = mysqli_real_escape_string($mysqli, $hashPword);

        if ($this->userExists($escapeUsername) == false) {

            $sql = " INSERT INTO tblusers (username, password)

        VALUES ('$escapeUsername', '$escapePword') ";

            $queryAccount = mysqli_query($mysqli, $sql);

            $lastInsertId  = mysqli_insert_id($mysqli);

            if ($queryAccount) {

                $ip = $_SERVER['REMOTE_ADDR'];
                $getLocation = $this->getLocation();
                $decodedLocation = json_decode($getLocation);
                $countryName =  $decodedLocation->country;
                $countryCity =  $decodedLocation->city;
                $Userlatitude =  $decodedLocation->latitude;
                $Userlongtitude =  $decodedLocation->longitude;

                $InsertLocation = $this->InsertLocation(
                    $lastInsertId,
                    $countryName,
                    $countryCity,
                    $Userlatitude,
                    $Userlongtitude,
                    $ip
                );

                $array = [
                    'success' => true,
                    'message' => 'Account created successfully',
                    'username' =>  $username,
                    'userid' => $lastInsertId
                ];

                $return = json_encode($array);
                echo "$return";
                exit();
            } else {

                $this->outputData(false, 'Unable to process', null);
                exit();
            }
        } else {

            $this->outputData(false, 'Username already exists, try another email', null);
            exit();
        }
    } catch (PDOException $e) {

        echo  $this->outputData(false, $_SESSION['err'] = $e->getMessage(), null);
        return false;
    }
}

    public function getLocation()
    {
        // Initialize cURL.
        $ch = curl_init();

        // Set the URL that you want to GET by using the CURLOPT_URL option.
        curl_setopt($ch, CURLOPT_URL, 'https://ipgeolocation.abstractapi.com/v1/?api_key=1ce8fe64be5f4631b1b733368022023c');

        // Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        // Execute the request.
        $data = curl_exec($ch);

        // Close the cURL handle.
        curl_close($ch);

        // Print the data out onto the page.
        return $data;
    }

    public  function InsertLocation($userid, $country_name, $country_city, $latitude, $longtitude, $ip)
    {

        $time = time();
        $sql = " INSERT INTO userlocation(userid, country_name, country_city, latitude, longtitude, ipaddress, time)
         VALUES(:userid, :country_name, :country_city, :latitude, :longtitude, :ip,  :time )";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':userid', $userid);
        $stmt->bindParam(':country_name', $country_name);
        $stmt->bindParam(':country_city', $country_city);
        $stmt->bindParam(':latitude', $latitude);
        $stmt->bindParam(':longtitude', $longtitude);
        $stmt->bindParam(':ip', $ip);
        $stmt->bindParam(':time', $time);
        if (!$stmt->execute()) {
            $stmt = null;
            $_SESSION['err'] = "Something went wrong, please try again..";
            return false;
        } else {

            return true;
        }
    }

    /*
@param mixed $mail
@param mixed $pword
@return false|array|void

*/
    public function tryLogin($username, $pword)
    {
        $Utility = new Utility();
        try {

            if ($this->userExists($username) == true) {

                $sql = "SELECT * from tblusers where username  = '$username'";
                $stmt = $this->connect()
                    ->prepare($sql);
                if (!$stmt->execute()) {
                    $stmt = null;
                    $_SESSION['err'] = "Something went wrong, please try again..";
                    return false;
                } else {
                    if ($stmt->rowCount() == 0) {

                        $stmt = null;
                        $_SESSION['err'] = "$username not found..";
                        return false;
                        // code...
                    } else {

                        if ($user = $stmt->fetchAll(PDO::FETCH_ASSOC)) {
                            $Utility = new Utility();
                            if (password_verify($pword, $user[0]['password'])) {

                                $ip = $_SERVER['REMOTE_ADDR'];
                                $getLocation = $this->getLocation();
                                $decodedLocation = json_decode($getLocation);
                               
                                $countryName =  $decodedLocation->country;
                                $countryCity =  $decodedLocation->timezone->name;
                                $Userlatitude =  $decodedLocation->latitude;
                                $Userlongtitude =  $decodedLocation->longitude;

                                $this->trackLogin($username, $countryName, $countryCity,  $Userlatitude, $Userlongtitude, $ip);
                                $user = $user[0];
                                $post_item = array(

                                    'username' => $username,
                                    'userid' => $user['id']
                                
                                );
                                return $post_item;
                            } else {
                                $_SESSION['err'] = "Incorrect password for $username";
                                return false;
                            }
                        }
                    }
                }
            } else {
                $_SESSION['err'] = "Unrecognised user";
                return false;
            }
        } catch (PDOException $e) {
            $Utility->outputData(false, $_SESSION['err'] = $e->getMessage(), null);
            return false;
        }
    }

    public function trackLogin($username, $country_name, $country_city, $latitude, $longtitude, $ip)
    {

        $time = time();
        $sql = " INSERT INTO login(username, country_name, country_city, latitude, longtitude, ipaddress, time)
         VALUES(:username, :country_name, :country_city, :latitude, :longtitude, :ip,  :time )";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':country_name', $country_name);
        $stmt->bindParam(':country_city', $country_city);
        $stmt->bindParam(':latitude', $latitude);
        $stmt->bindParam(':longtitude', $longtitude);
        $stmt->bindParam(':ip', $ip);
        $stmt->bindParam(':time', $time);
        if (!$stmt->execute()) {
            $stmt = null;
            $_SESSION['err'] = "Something went wrong, please try again..";
            return false;
        } else {

            return true;
        }
    }


    public function FetchIdByUser($username)
    {
        $sql = " SELECT  username  FROM tblusers ";
        $sql .= " WHERE  username = '{$username}'  ";
        $stmt = $this->connect()
            ->prepare($sql);
        if (!$stmt->execute()) {
            return false;
        } else {
            $result_set = $stmt->fetchColumn();
            return $result_set;
        }
    }
    
    
     public function extractUserid($username)
    {
        $sql = " SELECT *  FROM tblusers ";
        $sql .= " WHERE  username = '{$username}'  ";
        $stmt = $this->connect()
            ->prepare($sql);
        if (!$stmt->execute()) {
            return false;
        } else {
            $result_set = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $result_set = $result_set[0];
            $array = [
                  'userid'  => $result_set['id']
                ];
                
                return $array;
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

 

    public function checkMateUser($username)
    {
  
     if($this->userExists($username)){
        
         $this->outputData(false, 'An account already exists with this username', null);
        exit;

     }else{

       $this->outputData(true, 'New User', null);
        exit;

     }
    }
 

    // public function FetchUsrid($username)
    // {
    //     global $mysqli;

    //     $sql = " SELECT  *  FROM tblusers ";
    //     $sql .= " WHERE  id = '{$username}'  ";
    //     $result_set =  mysqli_query($mysqli, $sql);
    //     foreach ($result_set as $Found) {
    //         $array = array(
    //             'username' => $Found['username']

    //         );
    //         return  $array;
    //     }
    // }


    public function FetchIdByName($username)
    {
        global $mysqli;

        $sql = " SELECT  *  FROM tblusers ";
        $sql .= " WHERE  id = '{$username}'  ";
        $result_set =  mysqli_query($mysqli, $sql);
        foreach ($result_set as $Found) {
            $array = array(
                'username' => $Found['username']

            );
            return  $array;
        }
    }

    public function updateUser($fname, $lname, $email, $pword, $phone, $id)
    {

        $Utility = new Utility(); #Utility Class

        if ($Utility->validateEmail($email) == true) {

            if ($Utility->validatePhone($phone) == true) {

                if ($this->userExists($email) == false) {
                    $sql = " UPDATE users SET ";
                    $sql .= "fname  =  :fname, ";
                    $sql .= "lname = :lname, ";
                    $sql .= "mail =   :mail, ";
                    $sql .= "pword = :pword, ";
                    $sql .= " phone   = :phone ";
                    $sql .= "WHERE id = :id ";

                    $stmt = $this->connect()
                        ->prepare($sql);

                    $stmt->bindParam(':fname', $fname);
                    $stmt->bindParam(':lname', $lname);
                    $stmt->bindParam(':mail', $email);
                    $stmt->bindParam(':pword', $pword);
                    $stmt->bindParam(':phone', $phone);
                    $stmt->bindParam(':id', $id);
                    // return "New records updated successfully $name";
                    if (!$stmt->execute()) {

                        $Utility->outputData(false, 'Could not update account, please try again...', null);
                        return false;
                    } else {
                        $Utility->outputData(true, 'Record updated..', null);
                        return true;
                    }
                } else {
                    $Utility->outputData(false, 'Email already exists, try another email..', null);
                    exit;
                }
            } else {
                $Utility->outputData(false, 'Invalid phone number, try a new one..', null);
                exit;
            }
        } else {

            $Utility->outputData(false, 'Invalid email address, try a new one..', null);
            exit;
        }
    }
}
