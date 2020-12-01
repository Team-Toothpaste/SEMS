<!DOCTYPE html>
<html>
    <head>
        <title>Profile</title>
        <style></style>
    </head>
    <body>
        <div>
            <?php
                #$db_host        = '127.0.0.1';
                #$db_user        = 'root';
                #$db_pass        = '';
                #$db_database    = 'SEMS'; 
                #$db_port        = '3306';
                $conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
                $var = mysqli_query($conn, "SELECT * FROM users WHERE id = '2' ");    
            ?>
            <h2> My Profile </h2>
            <?php
                $data = mysqli_fetch_assoc($var);
                # echo "<div><img src = .$id['stored image'].></div>"; FOR LATER USE WITH IMAGES
            ?>
            <div>Username : 
                <?php
                    echo $data['username'];
                    
                ?>
            </div>
            
            <?php 
                echo "<table>";
                    echo "<tr>";
                        echo "<td>";
                            echo "First Name: ";
                        echo "</td>";
                        
                        echo "<td>";
                            echo $data['fname'];
                        echo "</td>";
                    echo "</tr>";

                    echo "<tr>";
                        echo "<td>";
                            echo "Last Name: ";
                        echo "</td>";
                        echo "<td>";
                            echo $data['lname'];
                        echo "</td>";
                    echo "</tr>";

                    echo "<tr>";
                        echo "<td>";
                            echo "Email: ";
                        echo "</td>";
                        echo "<td>";
                            echo $data['email'];
                        echo "</td>";

                    echo "</tr>";

                    echo "<tr>";
                        echo "<td>";
                            echo "Daily Income: ";
                        echo "</td>";

                        echo "<td>";
                            echo $data['dailyIncome'];
                        echo "</td>";

                    echo "</tr>";

                    echo "<tr>";
                        echo "<td>";
                            echo "Daily Outgoing: ";
                        echo "</td>";
                        
                        echo "<td>";
                            echo $data['dailyOutgoing'];
                        echo "</td>";
                    echo "</tr>";

                    echo "<tr>";
                        echo "<td>";
                            echo "Monthly Income: ";
                        echo "</td>";
                        
                        echo "<td>";
                            echo $data['monthlyIncome'];
                        echo "</td>";
                    echo "</tr>";

                    echo "<tr>";
                        echo "<td>";
                            echo "Monthly Outgoing: ";
                        echo "</td>";
                        
                        echo "<td>";
                            echo $data['monthlyOutgoing'];
                        echo "</td>";
                    echo "</tr>";

                    echo "<tr>";
                        echo "<td>";
                            echo "Monthly Budget: ";
                        echo "</td>";
                        
                        echo "<td>";
                            echo $data['monthlyBudget'];
                        echo "</td>";
                    echo "</tr>";

                echo "</table>";
            ?>
            
        </div>
    </body>
</html>