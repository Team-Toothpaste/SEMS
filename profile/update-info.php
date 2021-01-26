<?php
session_start();

$response = array('status' => 401, 'message' => 'User not logged in', 'data' => array());

require_once('../scripts/user.php');

$userObj = new User();

if($userObj->isLoggedIn()){
    //Logged in
    $response['status'] = 400;
    $response['message'] = "No data supplied";
    if(!empty($_POST)){
        $response['status'] = 500;
        $response['message'] = "Could not connect to database";
        $user = $userObj->getData();
        require_once('../.credentials.php');
        $conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
        if($conn){
            $response['message'] = "Could not load current user information";
            if(!empty($user)){
                $response['message'] = "Could not update user information";
                $hash = mysqli_real_escape_string($conn, $hash);
                //Logged in, verified
                $fields = array();
                $postfields = array();
                foreach($user as $key => $value){
                    array_push($fields);
                }
                foreach($_POST as $key => $value){
                    array_push($postfields);
                }
                $updatefields = array();
                foreach($postfields as $key){
                    if(in_array($key, $fields)){
                        array_push($updatefields, $key);
                    }
                }
                $updatestring = "";
                $continue = true;
                foreach($updatefields as $key){
                    $value = $postfields[$key];
                    $key = mysqli_real_escape_string($conn, $key);
                    $value = mysqli_real_escape_string($conn, $value);
                    switch(strtolower($key)){
                        case "email":
                            if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
                                $continue = false;
                            }
                        case "password":
                            //Hash password
                            $passwordSalt = substr(sha1(rand()), 0, 16); //Random salt
                            $value = crypt($value, "$6$" . $passwordSalt);
                            break;
                        case "username":
                            $checkUsernameNotExists = mysqli_query($conn, 'SELECT LOWER(username) FROM users WHERE LOWER(username)="' . strtolower($value) . '"');
                            if(mysqli_num_rows($checkUsernameNotExists) > 0){
                                $continue = false;
                            }
                            break;
                    }
                    if(!$continue){
                        break;
                    }
                    if(is_numeric($value)){
                        $updatestring .= "`" . $key . "`=" . $value . ",";
                    }
                    else{
                        $updatestring .= '`' . $key . '`="' . $value . '",';
                    }
                }
                if($continue){
                    $updatestring = 'UPDATE users SET ' . rtrim($updatestring, ',') . " WHERE id=" . $uid;
                    $update = mysqli_query($conn, $updatestring);
                    if($update){
                        $response['status'] = 200;
                        $response['message'] = "User information updated";
                        if(in_array('password', $updatefields)){
                            $message = <<<EOT
                            Hey, {$user['fname']}!

                            The password for your account on SEMS has been changed. If this wasn't you, please contact support.

                            SEMS Support
EOT;
                            $message = wordwrap($message, 70);
                            mail($user['email'], "Your password has been reset.", $message, "From: SEMS Support <no-reply@brookes-sems.epizy.com>\r\n");
                        }
                    }
                    else{
                        $response['message'] = "User information could not be updated.";
                        $response['data'] = mysqli_error($conn);
                    }
                }
            }
            mysqli_close($conn);
        }
    }
}
http_response_code($response['status']);
header("Content-type", "application/json");
echo json_encode($response);

?>