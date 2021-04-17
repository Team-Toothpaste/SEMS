<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php
	function debug_to_console($data) {
		$output = $data;
		if (is_array($output)){
			$output = implode(',', $output);
			
			echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
		}
	}
	require_once('../scripts/user.php');
	$user = new User();
	if(($user->isLoggedIn())){
		//$userID = $user->getUserID();
		$userId = mysqli_real_escape_string($conn, $user->getUserId());
		$_DB = array('location' => 'sql305.epizy.com', 'username' => 'epiz_27128658', 'password' => 'itNH1lHme8I', 'name' => 'epiz_27128658_sems');
		$conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
	
		if($conn){


			$category = $conn->real_escape_string($_POST['category']);
			$itemName = $conn->real_escape_string($_POST['itemName']);
			$itemDesc = $conn->real_escape_string($_POST['itemDesc']);
			$itemPrice = $conn->real_escape_string($_POST['itemPrice']);
	
		
		//check there are no duplicate items

			$query = "SELECT items.itemId FROM `transactions` INNER JOIN items ON items.itemId = transactions.itemId WHERE items.itemName = '$itemName' AND itemDesc = '$itemDesc' AND itemPrice = '$itemPrice'";

			$duplicate = mysqli_query($conn, $query);
			if(mysqli_num_rows($duplicate) == 0){	
				$items = mysqli_query($conn, "SELECT itemId FROM items");

				$itemID = mysqli_num_rows($items) + 1;
			
			
				//insert item into items table and update useritems table with both the userid and itemid
				$transactionSql = "INSERT INTO transactions(userId, itemId) VALUES ('$userID', '$itemID')";
				$itemsSql = "INSERT INTO items(itemId, categoryId, itemName, itemDesc, quantity, itemPrice) VALUES ('$itemID', '$category', '{$itemName}', '{$itemDesc}', 1, '{$itemPrice}')";
				if($conn->query($itemsSql) && $conn->query($transactionSql)){
					$conn->close();
					header('Location: index.php');
				}
			
			
			
				else{
					echo "Error: " .$itemsSql . "<br>" .mysqli_error($conn);
					echo "Error: " .$transactionSql . "<br>" .mysqli_error($conn);
				}
			}
			else if(mysqli_num_rows($duplicate) > 0){
				//send failure message
				echo "Duplicate entry";
				exit();
			}
		}
		else{
			die("Connection failed: " . mysqli_connect_error());
		}
	}
	else{
		header("location: http://brookes-sems.epizy.com/login/");
		exit();
	}
?>