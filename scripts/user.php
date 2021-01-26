<?php
session_start();

class User {
    
    private $loginId = null;
    
    function __construct(){
        $this->$loginId = $this->getLoginID();
    }
    
    function getLoginId(){
        if($this->isLoggedIn()){
            //return $_SESSION['sems-user'];
            return 'ABC:2';
        }
        else{
            return null;
        }
    }
    
    function getUserId(){
        if($this->isLoggedIn()){
            $parts = explode(':', $this->$loginId);
            return intval($parts[1]);
        }
        else{
            return null;
        }
    }
    
    function isLoggedIn(){
        //return (isset($_SESSION['sems-user']) && !empty($_SESSION['sems-user']) && isset($_COOKIE['sems-user']) && $_SESSION['sems-user'] == $_COOKIE['sems-user']);
        return true;
    }
    
}

?>