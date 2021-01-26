<!DOCTYPE html>
<html>
    <head>
        <title>Profile</title>
        <style></style>
    </head>
    <body>
        <div>
            <?php
                $userData = array();
                require_once('../.credentials.php');
                require_once('../scripts/user.php');
                $user = new User();
                if($user->isLoggedIn()){
                    $conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
                    if($conn){
                        $uId = mysqli_real_escape_string($conn, $user->getUserId());
                        $getUserData = mysqli_query($conn, "SELECT fname, lname, email, dailyIncome, dailyOutgoing, monthlyIncome, monthlyOutgoing, monthlyBudget, websiteStyle FROM users WHERE id =" . $uId); 
                        if(mysqli_num_rows($getUserData) == 1){
                            $userData = mysqli_fetch_assoc($getUserData);
                        }
                    }
                       
                }
            ?>
            
            <h2> My Profile </h2>
            <table>
                <?php
                    foreach($userData as $key => $value){
                        echo <<<EOT
                            <tr>
                                <td>$key: $value</td>
                            </tr>
EOT;
                    }
                ?>
            </table>

        </div>
    </body>
</html>