<?php
$responseCode = 400;
$responseMessage = "Form incomplete";
if(isset($_POST['fname']) && !empty($_POST['fname']) && isset($_POST['lname']) && !empty($_POST['lname']) && isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['password']) && !empty($_POST['password'])){
    //Vars all filled
    $responseMessage = "An unknown error occurred";
    $responseCode = 500;
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    //Format
    $fname = ucwords($fname);
    $lname = ucwords($lname);
    $email = strtolower($email);
    
    //Connect to DB
    require_once('.credentials.php');
    $conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
    if($conn){
        //DB connection
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            //Email valid
            if(preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/", $password)){
                //Password valid
                $fname = mysqli_real_escape_string($conn, $fname);
                $lname = mysqli_real_escape_string($conn, $lname);
                $email = mysqli_real_escape_string($conn, $email);
                
                //Hash password
                $passwordSalt = substr(sha1(rand()), 0, 16); //Random salt
				$password = crypt($password, "$6$" . $passwordSalt);
                
                $password = mysqli_real_escape_string($conn, $password);
                //Enter into DB
                $insert = mysqli_query($conn, 'INSERT INTO users (fname, lname, email, password) VALUES ("' . $fname . '", "' . $lname . '", "' . $email . '", "' . $password . '")');
                if($insert){
                    $responseCode = 200;
                    $responseMessage = "Registration successful";
                }
                else{
                    $responseMessage = mysqli_error($conn);
                }
            }
            else{
                $responseMessage = "Password invalid. Must include at least one lowercase and uppercase character, one number, and be at least 8 characters long.";
            }
        }
        else{
            $responseMessage = "Email invalid";
        }
    }
    mysqli_close($conn);
}
$response = array("status" => $responseCode, "response" => array('message' => $responseMessage, 'data' => $responseData));
header("Content-type", "application/json");
http_response_code($responseCode);
echo json_encode($response);
?>