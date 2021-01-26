<?php
session_start();

require_once('../.credentials.php');

class User {
    
    private $loginID = null;
    private $data = array();
    
    function __construct(){
        $this->loginID = $this->getLoginID();
    }
    
    function getInfo(){
        if($this->isLoggedIn()){
            if(empty($this->data)){
                $this->loadData();
            }
            return $this->data;
        }
    }
    
    function getLoginID(){
        if($this->isLoggedIn()){
            return $_SESSION['sems-user'];
        }
        else{
            return null;
        }
    }
    
    function getUserId(){
        if($this->isLoggedIn()){
            $parts = explode(':', $this->loginID);
            return intval($parts[1]);
        }
        else{
            return null;
        }
    }
    
    function loadData(){
        if($this->isLoggedIn()){
            $uid = explode(':', $_SESSION['sems-user'])[0];
            $hash = explode(':', $_SESSION['sems-user'])[1];
            if(!empty($uid) && !empty($hash)){
                $uid = mysqli_real_escape_string($conn, $uid);
                require_once('../.credentials.php');
                $conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
                if($conn){
                    $user = array();
                    $getuser = mysqli_query($conn, 'SELECT * FROM users WHERE id=' . $uid);
                    if(mysqli_num_rows($getuser) > 0){
                        $this->data = mysqli_fetch_assoc($getuser);
                    }
                    mysqli_close($conn);
                }
            }
        }
    }
    
    function isLoggedIn(){
        $loggedIn = false;
        if(isset($_SESSION['sems-user']) && !empty($_SESSION['sems-user']) && isset($_COOKIE['sems-user']) && $_SESSION['sems-user'] == $_COOKIE['sems-user']){
            try {
                $uid = explode(':', $_SESSION['sems-user'])[0];
                $hash = explode(':', $_SESSION['sems-user'])[1];
                $conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
                if($conn){
                    $auth = mysqli_query($conn, 'SELECT password, 2FACode FROM users WHERE id=' . $uid);
                    $userInfo = array();
                    if(mysqli_num_rows($auth) > 0){
                        $userInfo = mysqli_fetch_assoc($auth);
                    }
                    if(md5($userInfo['password'] . $userInfo['2FACode']) == $hash){
                        $loggedIn = true;
                    }
                    mysqli_close($conn);
                }
            }
            catch(Exception $e){
                $loggedIn = false;
            }
        }
        return $loggedIn;
    }
    
}

?>