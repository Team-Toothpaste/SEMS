<?php
$responseCode = 400;
$responseMessage = "Form incomplete";
if(isset($_POST['email']) && !empty($_POST['email'])){
    //Send reset email
    $email = $_POST['email'];
    
    //Format
    $email = strtolower($email);
    
    $responseMessage = "An unknown error occurred";
    $responseCode = 500;
    //Connect to DB
    require_once('../.credentials.php');
    $conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
    if($conn){
        //Show email sent as not to give a clue if email typed was correct
        $responseCode = 200;
        $responseMessage = "Email sent";
        //DB connection
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            //Email valid
            //Password valid
            $email = mysqli_real_escape_string($conn, $email);
            
            //Enter into DB
            $verifyemail = mysqli_query($conn, 'SELECT id, fname, lname, email, password FROM users WHERE email="' . $email . '"');
            if(mysqli_num_rows($verifyemail) > 0){
                while($row = mysqli_fetch_assoc($verifyemail)){
                    $uid = $row['id'];
                    $fname = $row['fname'];
                    $lname = $row['lname'];
                    $name = $fname . " " . $lname;
                    $email = $row['email'];
                    $password = $row['password'];
                    $hash = md5($name . $email . $password);
                    $message = <<<EOT
                    Hey, $fname!
                    
                    Here's your password reset link:
                    http://brookes-sems.epizy.com/password-reset/?u=$hash&i=$uid
                    
                    SEMS Support
EOT;
                    $message = wordwrap($message, 70);
                    if(!mail(" . $email . ", "Your password reset link.", $message, "From: SEMS Support <no-reply@brookes-sems.epizy.com>\r\n")){
                        $responseCode = 500;
                        $responseMessage = "Please try again later";
                    }
                }
            }
        }
        mysqli_close($conn);
    }
}
elseif(isset($_POST['uid']) && !empty($_POST['uid']) && is_numeric($_POST['uid']) && isset($_POST['password']) && !empty($_POST['password']) && isset($_POST['cpassword']) && !empty($_POST['cpassword']) && isset($_POST['hash']) && !empty($_POST['hash'])){
    //Update password
    $uid = $_POST['uid'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $hash = $_POST['hash'];
    
    $responseMessage = "Passwords don't match";
    
    if($password == $cpassword){
        $responseCode = 500;
        $responseMessage = "An unknown error occurred";
        
        //Connect to DB
        require_once('../.credentials.php');
        $conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
        if($conn){
            $responseCode = 401;
            $responseMessage = "Link invalid";
            $uid = mysqli_real_escape_string($conn, $uid);
            
            //Hash password
            $passwordSalt = substr(sha1(rand()), 0, 16); //Random salt
            $passwordHash = crypt($password, "$6$" . $passwordSalt);
            
            //Verify user details with hash
            $getdetails = mysqli_query($conn, 'SELECT fname, lname, email, password FROM users WHERE id=' . $uid . '');
            if(mysqli_num_rows($getdetails > 0)){
                while($row = mysqli_fetch_assoc($getdetails)){
                    $uhash = md5($row['fname'] . ' ' . $row['lname'] . $row['email'] . $row['password']);
                    $fname = $row['fname'];
                    $email = $row['email'];
                }
                if($uhash == $hash){
                    $responseCode = 500;
                    $responseMessage = "An unknown error occurred";
                    
                    //Update DB
                    $changePassword = mysqli_query($conn, 'UPDATE users SET password="' . $passwordHash . '" WHERE id=' . $uid);
                    if($changePassword){
                        $responseCode = 200;
                        $responseMessage = "Password changed";
                        $message = <<<EOT
                        Hey, $fname!

                        The password for your account on SEMS has been changed. If this wasn't you, please contact support.

                        SEMS Support
EOT;
                        $message = wordwrap($message, 70);
                        mail($email, "Your password has been reset.", $message, "From: SEMS Support <no-reply@brookes-sems.epizy.com>\r\n");
                    }
                }
            }
            mysqli_close($conn);
        }
    }
}
$response = array("status" => $responseCode, "response" => array('message' => $responseMessage, 'data' => null));
header("Content-type", "application/json");
http_response_code($responseCode);
echo json_encode($response);
?>