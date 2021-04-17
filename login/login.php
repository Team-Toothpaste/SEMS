<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once('../scripts/dependencies/third-party/PHPMailer/src/PHPMailer.php');
require_once('../scripts/dependencies/third-party/PHPMailer/src/SMTP.php');
require_once('../scripts/dependencies/third-party/PHPMailer/src/Exception.php');

function randNum() {
    return random_int(1000000,9000000);
};

session_start();
$responseMessage = "Form incomplete";

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
        $email = mysqli_real_escape_string($conn, $email);
        $result = mysqli_query($conn, 'SELECT userId, firstName, password FROM users WHERE email="' . $email . '" LIMIT 1');
        if ($result && mysqli_num_rows($result) > 0) {
            // Valid
            $row = mysqli_fetch_assoc($result);

            // Get matching first name from database
            $fname = $row['firstName'];
            $id = mysqli_real_escape_string($conn, $row['userId']);

            if (password_verify($password, $row['password'])) {
                // Update 2FA code
                $twofa = randNum();

                // Create string hash out of passwordhash:2FA
                // if using a salt it will have to be stored in database then
                // appended to the hash
                $hashedTwofa = md5($row['password'] . strval($twofa));
                // store twofa in database

                mysqli_query($conn, 'UPDATE users SET 2FACode="' . $twofa . '" WHERE userId=' . $id);
                        

                // Set session variables
                $_SESSION['sems-user'] = "{$id}:{$hashedTwofa}";

                // Set cookie 'sems-user'
                setcookie("sems-user", $id . ':' . $hashedTwofa, time() + (86400 * 30), "/");

                // PHPMailer
                $mail = new PHPMailer(true);

                //Server settings
                $mail->SMTPDebug = 0;
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host       = $_MAIL['location'];                     //Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                $mail->Username   = $_MAIL['username'];                     //SMTP username
                $mail->Password   = $_MAIL['password'];                     //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                $mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

                //Recipients
                $mail->setFrom('no-reply-sems@brookes.ac.uk', 'SEMS');
                $mail->addAddress($email, $fname);     //Add a recipient
                //    $mail->addAddress('ellen@example.com');               //Name is optional
                //    $mail->addReplyTo('info@example.com', 'Information');
                //    $mail->addCC('cc@example.com');
                //    $mail->addBCC('bcc@example.com');

                    //Attachments
                //    $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
                //    $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

                //Content
                $mail->isHTML(true);                                  //Set email format to HTML
                $mail->Subject = "SEMS: your 2-factor code";
                $mail->Body    = "Here is your code: {$twofa}";
                $mail->AltBody = "Here is your code: {$twofa}";

                $mail->send();

                // Redirect to 2FA page and send values
                $responseMessage = "Login successful";
                echo $responseMessage;
            }   
            else {
                // Invalid
                $responseMessage = "Incorrect details";
                echo $responseMessage;
            }
        }
        else {
            $responseMessage = "Incorrect details";
            echo $responseMessage;
        }
    }
    else {
        $responseMessage = "Server error";
        echo $responseMessage;
    }

} 
else {
    $responseMessage = "Details are empty";
    echo $responseMessage;
}
?>