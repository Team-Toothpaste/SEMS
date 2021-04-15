<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html>
	<head>
        <link rel="stylesheet" href="//brookes-sems.epizy.com/styles/main.css">
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
    <style>
        .table-hover tbody tr:hover td:not(:nth-child(7)) {
            background: gold;
        }

        table{border: none; border-collapse: collapse;}
        th{
            background-color: gold;
            color: black;
            border: 3px solid black;
    
        }
        table td{
            border-left: 3px solid black;
        }

        .inputTable{
            border-color: blue;
        }
        .content{
            position: relative;
            left: 2px;
            top: 2px;
        }
        .total{
            position: absolute;
            display: inline-block;
            bottom: -30px;
            left: 100px;
            background-color: gold;

        }
        .inputButton{
            position: absolute;
            bottom: -45px;
}
        .boxPopup {
            display: none;
            position: fixed; 
            z-index: 1;
            padding-top: 100px; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4); 
        }   
        .box-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }


        .close {
            color: #aaaaaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
         }
</style>

	</head>	
	<body>
		<?php
			$_DB = array('location' => 'sql305.epizy.com', 'username' => 'epiz_27128658', 'password' => 'itNH1lHme8I', 'name' => 'epiz_27128658_sems');
			$conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
			$query = "SELECT firstName, lastName, sum(items.itemPrice), count(items.itemId) FROM transactions INNER JOIN users ON users.userId = transactions.userId INNER JOIN items ON items.itemId = transactions.itemId GROUP BY transactions.userId ORDER BY sum(items.itemPrice) ASC, count(items.itemId) ASC";
			echo '<div class = content><table class="table-hover"> 
      		<tr> 
          		<th> Ranking </th> 
          		<th> First name </th> 
          		<th> Last name</th>  
                <th> amount spent </th>  
                <th> transactions qty </th> 
             
      		</tr>';
            $count = 0;
			if($result = $conn->query($query)){
				while($row = $result->fetch_assoc()){
					$field1 = $row["firstName"];
					$field2 = $row["lastName"];
					$field3 = $row["sum(items.itemPrice)"];
					$field4 = $row["count(items.itemId)"];
					
					$count = $count + 1;
				
				    echo '<tr> 
                        <td>'.$count.'</td>
					    <td>'.$field1.'</td> 
					    <td>'.$field2.'</td> 
					    <td>'.$field3.'</td> 
					    <td>'.$field4.'</td> 
					    					    
				    </tr>';
				}
			}
			
            //change userID to variable once login has been implemented
            
			mysqli_close($conn);
            
            
		?>
	</body>
</html>