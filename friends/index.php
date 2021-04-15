<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
?>
<?php
    function isFriend($userId, $friendId){
        $_DB = array('location' => 'sql305.epizy.com', 'username' => 'epiz_27128658', 'password' => 'itNH1lHme8I', 'name' => 'epiz_27128658_sems');
        $conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
        $query = "SELECT friendId FROM friends WHERE username1 = '$userId' AND username2 = '$friendId' AND status = 1";
        if($friendCheck = mysqli_query($conn, $query)){
            if(mysqli_num_rows($friendCheck) > 0){
                return true;
            }
        }
        return false;
    }
?>
<?php
    function isRequestSent($userId, $friendId){
        $_DB = array('location' => 'sql305.epizy.com', 'username' => 'epiz_27128658', 'password' => 'itNH1lHme8I', 'name' => 'epiz_27128658_sems');
        $conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
        $query = "SELECT friendId FROM friends WHERE username1 = '$userId' AND username2 = '$friendId'";
        if($friendCheck = mysqli_query($conn, $query)){
            if(mysqli_num_rows($friendCheck) > 0){
                return true;
            }
        }
        return false;
    }
?>
<?php
    function getUsersName($userId){
        $_DB = array('location' => 'sql305.epizy.com', 'username' => 'epiz_27128658', 'password' => 'itNH1lHme8I', 'name' => 'epiz_27128658_sems');
        $conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
        $query = "SELECT firstName FROM users WHERE userId = '$userId'";
        if($result = $conn->query($query)){
            $username = $result->fetch_assoc();
            return $username["firstName"];
        }
    }
?>


<!DOCTYPE HTML>
<html>
    <head>
        <link rel="stylesheet" href="//brookes-sems.epizy.com/styles/main.css">
        <style>            

            table{border: none; float:left; border-collapse: collapse;}
            th a{
                display:block;
                width: 100%;
            }
            th{
                background-color: gold;
                color: black;
                border: 3px solid black;
                
            }
            table tr{
                border: 3px solid black;
            }

        </style>
        <script>
            function submitData(){
				var x;
				if(window.XMLHttpRequest){
					x = new XMLHttpRequest();
				}
				else{
					x = new ActiveXObject("Microsoft.XMLHTTP");
				}
				console.log(x);
				x.open('POST', 'input.php');
				var form = document.getElementById('input-data');
				
				x.onreadystatechange = function(){
                    if(this.readyState == 4){
                        console.log(this.responseText);
                        for(let responseElem of document.querySelectorAll('form#input-data p.response')){
                            if(responseElem.hasAttribute('success')) responseElem.removeAttribute('success');
                            switch(this.status){
                                case 200:
                                    responseElem.setAttribute('success', '');
                                default:
                                    responseElem.innerText = JSON.parse(this.responseText).response.message;
                            }
                        };
                    }
                };
				
				
				const formData = new FormData(form);
				console.log(formData);
				x.send(formData);
			}
		</script>
    </head>

    <body>
        <?php $userID = 3; ?>
        <p>Logged in as: <b><?php echo getUsersName($userID) ?> </b> | UserID:  <?php echo $userID ?></p>
        <?php
            require_once('../scripts/user.php');
            //$user = new User();
            //if(!($user->isLoggedIn()))       
                $_DB = array('location' => 'sql305.epizy.com', 'username' => 'epiz_27128658', 'password' => 'itNH1lHme8I', 'name' => 'epiz_27128658_sems');
                $conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
                
                
                
                $query = "SELECT firstName, userId FROM users WHERE userId != '$userID'";
                
                
                
                
            
                
                echo '                    
                    <table>
                        <tr>    
                            <th>
                                
                                available friends
                                
                            </th>
                            
                        </tr>';
                if($result = $conn->query($query)){
                    while($row = $result->fetch_assoc()){
                        $name = $row["firstName"];
                        $friendID = $row["userId"];
                        //show a list of available friends based on whether a user has sent a request and received a request
                        if(!isRequestSent($userID, $friendID) && !isRequestSent($friendID, $userID)){
                            echo '<tr> 
                                <td>'.$name.'</td> 
                                <td>UserId: | '.$friendID.'</td>
                                <td> 
                                    <form id = "input-data" action = "confirmRequest.php" method = "POST" onsubmit="(event.preventDefault();submitData();return false;)">
                                        <input type="hidden" name= "user" value= '.$userID.'/>      
                                        <input type="hidden" name= "request" value= '.$friendID.'/>
                                        
                                        <input type="submit" name= "requestType" value ="addFriend" />
                                        
                                    </form>
                            
                                </td>
    
                            </tr>';
                        }                    

                    }
                }
                $result->free();
                echo '  </table>                  
                    <table>
                        <tr>    
                            <th>
                                friends list
                            </th>
                        </tr>';
                $friendQuery = "SELECT username2 FROM friends WHERE username1 = '$userID' AND status = 1";
                if($result = $conn->query($friendQuery)){
                    while($row = $result->fetch_assoc()){
                        $friendID = $row["username2"];
                        if(isFriend($userID, $friendID)){
                            
                            echo '<tr> 
                                <td>'.getUsersName($friendID).'</td>   
                                <td>UserId: | '.$friendID.'</td>
                                <td>
                                    <form id = "input-data" action = "confirmRequest.php" method = "POST" onsubmit="(event.preventDefault();submitData();return false;)">
                                    <input type="hidden" name= "user" value= '.$userID.'/>      
                                    <input type="hidden" name= "request" value= '.$friendID.'/>
                                    <input type="submit" name= "requestType" value ="remove" />
                            </form>
                                
                                </td>
            
                            </tr>';
                        }      
        
                    }
                }
                $result->free();
                echo ' </table>                   
                <table>
                    <tr>    
                        <th>
                            friend requests
                        </th>
                    </tr>';

                $friendRequests = "SELECT username1 FROM friends WHERE username2 = '$userID' AND status = 0";
                if($result = $conn->query($friendRequests)){
                    while($row = $result->fetch_assoc()){
                        $requestID = $row["username1"];
                        if(!isFriend($userID, $requestID)){
                        echo '<tr> 
                                <td>'.getUsersName($requestID).'</td>    
                                <td>UserId: | '.$requestID.' </td>                                 
                                <td> 
                                <form id = "input-data" action = "confirmRequest.php" method = "POST" onsubmit="(event.preventDefault();submitData();return false;)">
                                    <input type="hidden" name= "user" value= '.$userID.'/>      
                                    <input type="hidden" name= "request" value= '.$requestID.'/>
                                    <input type="submit" name= "requestType" value ="accept" />
                                </form>                        
                                </td>
                                <td> 
                                <form id = "input-data" action = "confirmRequest.php" method = "POST" onsubmit="(event.preventDefault();submitData();return false;)">
                                    <input type="hidden" name= "user" value= '.$userID.'/>      
                                    <input type="hidden" name= "request" value= '.$requestID.'/>
                                    <input type="submit" name= "requestType" value ="decline" />
                                </form>                        
                                </td>
                            </tr>';
                    
                        }
                    }
                    echo '</table>';
                }
                $result->free();
            
        ?>

    </body>



</html>