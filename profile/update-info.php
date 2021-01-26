<?php
session_start();

$response = array('status' => 401, 'message' => 'User not logged in', 'data' => array());

if(isset($_SESSION['sems-user']) && !empty($_SESSION['sems-user']) && isset($_COOKIE['sems-user']) && $_SESSION['sems-user'] === $_COOKIE['sems-user']){
    //Logged in
    $reponse['status'] = 400;
    $response['message'] = "No data supplied";
    if(!empty($_POST)){
        $response['status'] = 401;
        $response['message'] = "User not logged in";
        $uid = explode(':', $_SESSION['sems-user'])[0];
        $hash = explode(':', $_SESSION['sems-user'])[1];
        if(!empty($uid) && !empty($hash)){
            $uid = mysqli_real_escape_string($conn, $uid);
            $response['status'] = 500;
            $response['message'] = "Could not connect to database";
            require_once('../.credentials.php');
            $conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
            if($conn){
                $response['message'] = 'Could not retrieve user information';
                $user = array();
                $getuser = mysqli_query($conn, 'SELECT * FROM users WHERE id=' . $uid);
                if(mysqli_num_rows($getuser) > 0){
                    $user = mysqli_fetch_assoc($getuser);
                }
                if(!empty($user)){
                    $response['status'] = 401;
                    $response['message'] = "User not authenticated";
                    if(md5($user['password'] . $user['2FACode']) == $hash){
                        $response['status'] = 500;
                        $hash = mysqli_real_escape_string($conn, $hash);
                        //Logged in, verified
                        $fields = array();
                        $postfields = array();
                        foreach($user as $key => $value){
                            array_push($fields, strtolower($key));
                        }
                        foreach($_POST as $key => $value){
                            array_push($postfields, strtolower($key));
                        }
                        $updatefields = array();
                        foreach($postfields as $key){
                            if(in_array($key, $fields)){
                                array_push($updatefields, $key);
                            }
                        }
                        $updatestring = "";
                        foreach($updatefields as $key){
                            $value = $postfields[$key];
                            if($key == 'password'){
                                //Hash password
                                $passwordSalt = substr(sha1(rand()), 0, 16); //Random salt
                                $value = crypt($value, "$6$" . $passwordSalt);
                            }
                            $key = mysqli_real_escape_string($conn, $key);
                            $value = mysqli_real_escape_string($conn, $value);
                            if(is_numeric($value)){
                                $updatestring .= "`" . $key . "`=" . $value . ",";
                            }
                            else{
                                $updatestring .= '`' . $key . '`="' . $value . '",';
                            }
                        }
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
            }
        }
    }
}
http_response_code($response['status']);
header("Content-type", "application/json");
echo json_encode($response);

?>