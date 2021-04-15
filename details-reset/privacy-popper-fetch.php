<?
require_once('../.credentials.php');
$conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
    if ($conn) {
        // use session data to get userId
        $userId = $_SESSION['sems-user'].explode()[0];
    }
} 

function accountGather() {
    $result = mysqli_query($conn, "SELECT * FROM users WHERE userId = '".$userId."'");
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
}
function profileGather() {

}
function settingsGather() {

}
?>