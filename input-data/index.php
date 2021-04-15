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


            .radiobuttons input[type="radio"]{
                opacity:0;
                position: fixed;
            }
            .radiobuttons label{
                display: inline-block;
                background-color: grey;
                color: black;
                padding: 5px 5px;
                font-family: sans-serif, Arial;
                font-size: 16px;
                border: 2px solid black;
                border-radius: 4px;
            }
            .radiobuttons input[type="radio"]:checked + label {
                background-color:gold;
                border: 3px solid black;
            }

            .radiobuttons label:hover {
                background-color: gold;
            }
            


            .button {

                height: 40px;

                line-height: unset;

                background-color: var(--gold, gold);

                border: none;

                cursor: pointer;

                color: var(--white, white);

                border-radius: 20px;

            }
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
			$query = "SELECT * FROM items";
			echo '<div class = content><table class="table-hover"> 
      		<tr> 
          		<th> item number </th> 
          		<th> category </th> 
          		<th> name</th>  
                <th> desc </th>  
                <th> price </th> 
                <th> time added </th>  
      		</tr>';
			if($result = $conn->query($query)){
				while($row = $result->fetch_assoc()){
					$field1 = $row["item_id"];
					$field2 = $row["category_id"];
					$field3 = $row["itemName"];
					$field4 = $row["descript"];
					$field5 = $row["price"];
					$field6 = $row["timeadded"];
				
				echo '<tr> 
					<td>'.$field1.'</td> 
					<td>'.$field2.'</td> 
					<td>'.$field3.'</td> 
					<td>'.$field4.'</td> 
					<td>'.$field5.'</td> 
					<td>'.$field6.'</td> 
                    <td> <button class = button>Delete placeholder</button> </td>

				</tr>';
				}
			}
			$result->free();
            //change userID to variable once login has been implemented
            $query = "SELECT sum(items.price) FROM items INNER JOIN userItems ON userItems.itemId = items.item_id AND userItems.userID = 1";
            if($result = $conn->query($query)){
                $row = mysqli_fetch_row($result);
                $sum = $row[0];

                echo '<div class = total> Total: £', $sum, "</div>";
            }
			mysqli_close($conn);
            
            
		?>
        
            
        <div class = inputButton>
		<button class = button id="button">Input Data</button>
        </div></div>
		<div id="popup" class="boxPopup">
		<div class="box-content">
			<span class="close">&times;</span>
			<form id = "input-data" method ="POST" action = "input.php" onsubmit="(event.preventDefault();submitData();return false;)">
				<label for = "itemName"> Item Name </label><br>
				<input type="text" id = "itemName" name = "itemName" required><br>
				<label for= "itemPrice"> Item Price </label><br>
                <input type="text" id = "itemPrice" name = "itemPrice" required min = "0" step = ".01"><br>
                <div class = radiobuttons>
				    <input type="radio" id ="category1" name = "category" value = "1" checked>
				    <label for= "category1"> Category1 </label>
				    <input type="radio" id ="category2" name = "category" value = "2" >
				    <label for= "category2"> Category2 </label>
				    <input type="radio" id ="category3" name = "category" value = "3" >
				    <label for= "category3"> Category3 </label>
				    <input type="radio" id ="category4" name = "category" value = "4">
				    <label for= "category4"> Category4 </label>
				    <input type="radio" id ="category5" name = "category" value = "5">
				    <label for= "category5"> Category5 </label>
				    
                </div><br>
				<label for = "itemDesc"> Item Description </label><br>
				<input type="text" id = "itemDesc" name = "itemDesc" required><br>
				<input type="submit" value="Submit">
			</form>        

		</div>        

		<script>
			
			var modal = document.getElementById("popup");
			var button = document.getElementById("button");
			var span = document.getElementsByClassName("close")[0];
			button.onclick = function() {
  				modal.style.display = "block";
			}

			span.onclick = function() {
  				modal.style.display = "none";
			}

			window.onclick = function(event) {
  				if (event.target == modal) {
    				modal.style.display = "none";
  				}
			}
		</script>


	</body>
</html>