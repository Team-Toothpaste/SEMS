<?php
session_start();
chdir('/home/vol6_3/epizy.com/epiz_27128658/htdocs');
require_once('.credentials.php');
require_once('scripts/user.php');

$response = array("status" => 401, "data" => array(), "message" => "Not logged in");

$user = new User();
if($user->isLoggedIn()){
    $response['status'] = 500;
    $response['message'] = "Could not connect to database";
    $conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
    if($conn){
        $response['status'] = 200;
        $getitems = mysqli_query($conn, "SELECT transactions.*, items.* FROM transactions INNER JOIN items ON transactions.itemId=items.itemId WHERE transactions.userId = " . $user->getUserId() . " ORDER BY items.timeAdded DESC");
        if(mysqli_num_rows($getitems) > 0){
            $response['data']['items'] = array();
            while($item = mysqli_fetch_assoc($getitems)){
                array_push($response['data']['items'], $item);
            }
            $response['data']['user'] = $user->getInfo(true);
            $response['message'] = "Success";
        }
        else{
            $response['message'] = 'No items to show';
        }
    }
}

http_response_code($response['status']);
header("Content-type: application/json");
echo json_encode($response);

?>