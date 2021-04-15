<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php
    $_DB = array('location' => 'sql305.epizy.com', 'username' => 'epiz_27128658', 'password' => 'itNH1lHme8I', 'name' => 'epiz_27128658_sems');
	$conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
	if($conn){
        $user = $conn->real_escape_string($_POST['user']);
		$request = $conn->real_escape_string($_POST['request']);
        $requestType = $conn->real_escape_string($_POST['requestType']);
        switch ($requestType) {
            case "addFriend":
                $query = "INSERT INTO friends(username1, username2, status) VALUES ('$user', '$request', 0)";        
                if($conn->query($query)){
                    $conn->close();
                    header('Location: index.php');
                }
                else{
                    echo "Error: " .$query . "<br>" .mysqli_error($conn);
                }
                break;

            case "accept":
                //add to new requester to friends and set status to 1
                $query = "INSERT INTO friends(username1, username2, status) VALUES ('$user', '$request', 1)";
                //set status of requester to 1
                $query2 = "UPDATE friends SET status = 1 WHERE username1 = '$request' AND username2 = '$user'";
                if($conn->query($query) && $conn->query($query2)){            
                    $conn->close();
                    header('Location: index.php');
                }
                else{
                    echo "Error: " .$query . "<br>" .mysqli_error($conn);
                }
                break;
            
            
            
            case "decline":
                $query = "DELETE FROM friends WHERE username1 = '$request' AND username2 = '$user'";
                if($conn->query($query)){
                    $conn->close();
                    header('Location: index.php');
                }
                else{
                    echo "Error: " .$query . "<br>" .mysqli_error($conn);
                }
                break;
            
            case "remove":
                //delete records relating to both users in friend request
                $query = "DELETE FROM friends WHERE (username1 = '$request' AND username2 = '$user') OR (username1 = '$user' AND username2 = '$request')";
                if($conn->query($query)){
                    $conn->close();
                    header('Location: index.php');
                }
                else{
                    echo "Error: " .$query . "<br>" .mysqli_error($conn);
                }
                break;
                
        }

    }

?>