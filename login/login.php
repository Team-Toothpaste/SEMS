<?php

function randNum() {
    return random_int(1000000,9000000);
}

session_start();

require_once('../.credentials.php');
if( isset($_POST['email']) && !empty($_POST['email'])
&& isset($_POST['password']) && !empty($_POST['password'] )){
    // Get login details from form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Reformat
    $email = strtolower($email);

    // Connect to database
    $conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
    if ($conn) {
        // Compare posted values to see if they exist in users table
        $result = mysqli_query($conn, "SELECT id, password FROM users WHERE email = '".$email."'");
        if ($result && mysqli_num_rows($result) > 0) {
            // Valid
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            // Get matching first name from database
            $fname = $row['fname'];
            $id = $row['id'];

            if (md5($password) == $row['password']) {
                // Update 2FA code
                $twofa = randNum();

                // Create string hash out of passwordhash:2FA
                // if using a salt it will have to be stored in database then
                // appended to the hash before dehashing
                $hashedTwofa = md5(strval($password) . strval($twofa));
                // store hashed twofa in database
                // mysqli_query($conn, "INSERT INTO users (2FACode) VALUES ({$hashedTwofa})");
                        

                // Set session variables
                $_SESSION['sems-user'] = $hashedTwofa;

                // Set cookie 'sems-user'
                setcookie("sems-user",$twofa . $password . $id);

                // phpmailer

                // Redirect to 2FA page and send values
                ///////////////////////////////// this is probably not a good solution as 
                //only GET possible, so hash is in url and cannot be longer than certain length
                // cookie is global
                // $arr = array('action'=>'redirect','location'=>'../2fa/index.php');
                // $json = json_encode($arr);
                $json = json_encode(array('action'=>'redirect','location'=>'/2fa/index.php'));
                echo $json; exit();
            }
            else {
                $json = json_encode(array('action'=>'display','html'=>'Incorrect information.'));
                echo $json; exit();
            }
        }

            
        else {
            // Invalid
            $json = json_encode(array('action'=>'display','html'=>'There was an error.'));
            echo $json; exit();

        }
        
    }
    else {
        $json = json_encode(array('action'=>'display','html'=>'Could not connect to database.'));
        echo $json; exit();
    }

} else {
    $json = json_encode(array('action'=>'display','html'=>'Please enter values.'));
    echo $json; exit();
}
?>