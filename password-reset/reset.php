<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once('scripts/dependencies/third-party/PHPMailer/src/PHPMailer.php');
require_once('scripts/dependencies/third-party/PHPMailer/src/SMTP.php');
require_once('scripts/dependencies/third-party/PHPMailer/src/Exception.php');

$responseCode = 400;
$responseMessage = "Form incomplete";

$mailReady = false;
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = $_MAIL['location'];                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = $_MAIL['username'];                     //SMTP username
    $mail->Password   = $_MAIL['password'];                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    $mail->setFrom('no-reply-sems@brookes.ac.uk', 'SEMS');
    $mailReady = true;
}
catch(Exception $e){
    $reponsecode = 500;
    $responseMessage = "Mailer failed to initiate.";
}

if(isset($_POST['email']) && !empty($_POST['email']) && $mailReady){
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
                    
                    try {
                        
                        
                        //Recipients
                        $mail->addAddress($email, $fname . ' ' . $lname);     //Add a recipient
                        
                        //Content
                        $mail->isHTML(true);                                  //Set email format to HTML
                        $mail->Subject = 'Reset your SEMS password...';
                        $mail->Body    = $message;
                        $mail->AltBody = wordwrap($message, 70);

                        $mail->send();
                    }
                    catch (Exception $e) {
                        //Do nothing
                        echo $e;
                    }
                    
                }
            }
        }
        mysqli_close($conn);
    }
}
elseif(isset($_POST['uid']) && !empty($_POST['uid']) && is_numeric($_POST['uid']) && isset($_POST['password']) && !empty($_POST['password']) && isset($_POST['cpassword']) && !empty($_POST['cpassword']) && isset($_POST['hash']) && !empty($_POST['hash']) && $mailReady){
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
                    $lname = $row['lname'];
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
                        
                        try {
                            //Recipients
                            $mail->addAddress($email, $fname . ' ' . $lname);     //Add a recipient

                            //Content
                            $mail->isHTML(true);                                  //Set email format to HTML
                            $mail->Subject = 'Your SEMS password has been reset.';
                            $mail->Body    = $message;
                            $mail->AltBody = wordwrap($message, 70);

                            $mail->send();
                        }
                        catch (Exception $e) {
                            //Do nothing
                        }
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