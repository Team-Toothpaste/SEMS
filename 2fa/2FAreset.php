<?php
$responseCode = 400;
$responseMessage = "Form incomplete";
if(isset($_POST['2FACode']) && !empty($_POST['2FACode']) && is_numeric($_POST['2FACode'])){
    $2FACode = $_POST['2FACode'];
    //Format
    $responseMessage = "An unknown error occurred";
    $responseCode = 500;
    //Connect to DB
    require_once('../.credentials.php');
    $conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
    if($conn){
        $responseCode = 200;
        //DB connection
        $cookiedata = $_COOKIE['sems-user'];
        $cookieParts = explode(':', $cookieData);
        $uid = $cookieParts[0];
        $getpassword = SELECT password FROM users WHERE userId=$uid
        if(mysqli_num_rows($getpassword) == 1){
            //Search for 2FA code for user with id cookie[1] Check md5($passwordhash . $2facode) == cookie[0] if true continue: $_SESSION['sems-user'] -> cookie
            $password = mysqli_fetch_assoc($getpassword)['password']
            if(md5($passwordhash . $2FACode) == $cookieParts[1]){
                $_SESSION['sems-user'] = $cookieData;
                $responseMessage = "Code correct"
            } 
            else{
                $responseMessage = "Code incorrect"
            }
        }
        mysqli_close($conn);
    }
}
$response = array("status" => $responseCode, "response" => array('message' => $responseMessage, 'data' => null));
header("Content-type", "application/json");
http_response_code($responseCode);
echo json_encode($response);
?>