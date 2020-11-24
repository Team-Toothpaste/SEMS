<?php
$responseCode = 400;
$responseMessage = "Form incomplete";
if(isset($_POST['email']) && !empty($_POST['email'])){
    //Vars all filled
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
            $verifyemail = mysqli_query($conn, 'SELECT fname, lname, email, password FROM users WHERE email="' . $email . '"');
            if(mysqli_num_rows($verifyemail) > 0){
                while($row = mysqli_fetch_assoc($verifyemail)){
                    $fname = $row['fname'];
                    $lname = $row['lname'];
                    $name = $fname . " " . $lname;
                    $email = $row['email'];
                    $password = $row['password'];
                    $hash = md5($name . $email . $password);
                    $message = <<<EOT
                    Hey, $fname!
                    
                    Here's your password reset link:
                    http://brookes-sems.epizy.com/password-reset/?u=$hash
                    
                    SEMS Support
EOT;
                    $message = wordwrap($message, 70);
                    mail(" . $email . ", "Your password reset link.", $message, "From: SEMS Support <no-reply@brookes-sems.epizy.com>\r\n");
                }
            }
        }
    }
    mysqli_close($conn);
}
$response = array("status" => $responseCode, "response" => array('message' => $responseMessage, 'data' => null));
header("Content-type", "application/json");
http_response_code($responseCode);
echo json_encode($response);
?>