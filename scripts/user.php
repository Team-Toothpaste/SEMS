<?php
session_start();

class User {
    
    private $loginID = null;
    private $data = array();
    private $publicData = array();
    private $db = array('location' => 'sql305.epizy.com', 'username' => 'epiz_27128658', 'password' => 'itNH1lHme8I', 'name' => 'epiz_27128658_sems');
    private $conn = null;
    
    function __construct(){
        $this->loginID = $this->getLoginID();
        $this->loadData();
    }

    function closeConn(){
        if($this->conn){
            mysqli_close($this->conn);
            $this->conn = null;
        }
    }

    function getConn(){
        if(!$this->conn || is_null($this->conn)){
            $this->conn = mysqli_connect($this->db['location'], $this->db['username'], $this->db['password'], $this->db['name']);
        }
        return $this->conn;
    }
    
    function getInfo($publicOnly=false){
        if($this->isLoggedIn()){
            if(empty($this->data) || empty($this->publicData)){
                $this->loadData();
            }
            if($publicOnly){
                return $this->publicData;
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
            $parts = explode(':', $this->getLoginID());
            return intval($parts[0]);
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
                $uid = mysqli_real_escape_string($this->getConn(), $uid);
                if($this->getConn()){
                    $user = array();
                    $getuser = mysqli_query($this->getConn(), 'SELECT * FROM users WHERE userId=' . $uid);
                    if(mysqli_num_rows($getuser) > 0){
                        $this->data = mysqli_fetch_assoc($getuser);
                        $this->publicData['firstName'] =$this->data['firstName'];
                        $this->publicData['lastName'] =$this->data['lastName'];
                        $this->publicData['username'] =$this->data['username'];
                    }
                    $this->closeConn();
                }
            }
        }
    }
    
    function isLoggedIn(){
        if(session_status() !== PHP_SESSION_ACTIVE || session_status() === PHP_SESSION_NONE){
            session_start();
        }
        $loggedIn = false;
        if(isset($_SESSION['sems-user']) && !empty($_SESSION['sems-user']) && isset($_COOKIE['sems-user']) && $_SESSION['sems-user'] == $_COOKIE['sems-user']){
            try {
                $uid = explode(':', $_SESSION['sems-user'])[0];
                $hash = explode(':', $_SESSION['sems-user'])[1];
                if($this->getConn()){
                    $auth = mysqli_query($this->getConn(), 'SELECT password, 2FACode FROM users WHERE userId=' . $uid . ' LIMIT 1');
                    if(mysqli_num_rows($auth) > 0){
                        $userInfo = mysqli_fetch_assoc($auth);
                        if(md5($userInfo['password'] . $userInfo['2FACode']) == $hash){
                            $loggedIn = true;
                        }
                    }
                    $this->closeConn();
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