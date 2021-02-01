<?php

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
        $result = mysqli_query($conn, "SELECT id FROM users WHERE email = '".$email."' AND password = '".$password."'");
        if ($result && mysqli_num_rows($result) > 0) {
            // Valid
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            // Get matching first name from database
            $fname = $row['fname'];

            // Update 2FA code
            $twofa = uniqid();

            // Create string hash out of passwordhash:2FA
            // if using a salt it will have to be stored in database then
            // appended to the hash before dehashing
            $hashedTwofa = crypt(strval($password) . strval($twofa));
                    
            session_start();    
            // Set session variables
            $_SESSION['sems-user'] = $hashedTwofa;

            // Set cookie 'sems-user'
            setcookie("cookieIdentity",$hashedTwofa,time() + (86400*30),"/");

            // Redirect to 2FA page and send values
            ///////////////////////////////// this is probably not a good solution as 
            //only GET possible, so hash is in url and cannot be longer than certain length
            // cookie is global
            header("location: ../2fa/index.php?vals=" . http_build_query({$hashedTwofa,$fname}));
        }
        else {
            // Invalid
            echo "The information you entered is incorrect."; exit();

        }
        
    }
    else {
        echo mysqli_error($conn); exit();
    }

} else {
    echo "Please enter values.";
}
?>
    