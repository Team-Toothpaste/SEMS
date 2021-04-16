<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php
    $userData = array();
    require_once('../scripts/user.php');
    require_once('../.credentials.php');
    $user = new User();
    //change !$user to $user
    if(($user->isLoggedIn())){
        $conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
        if($conn){
            $uId = mysqli_real_escape_string($conn, $user->getUserId());

            //get all item, category and sector names
            // select user.userId, transactions.transactionId, items.transactionId, items.itemId, items.itemName, items.categoryId, category.categoryId, category.categoryName, category.secCatId, sectorCategories.secCatId, sectorCategories.categoryName
            // from user where userId = $uId
            // inner join transactions on user.userId = transactions.userId
            // inner join items on transactions.transactionId = items.transactionId
            // inner join category on items.itemId = category.itemId
            // inner join itemCategories on sectorCategories.secCatId = itemCategories.secCatId;
            $getUserData = mysqli_query($conn, "SELECT firstName, lastName, userUni, userCourse, email, monthlyBudget, monthlyIncome, monthlyOutgoing, dailyIncome, dailyOutgoing, profileImg FROM users WHERE userId =" . $uId);
            if(mysqli_num_rows($getUserData) == 1){
                $userData = mysqli_fetch_assoc($getUserData);
                if($userData["monthlyOutgoing"] == 0 && $userData["monthlyBudget"] == 0){
                    $balacePercentage = 0;
                }
                else{
                    $balacePercentage = (floatval($userData["monthlyOutgoing"])/floatval($userData["monthlyBudget"]))*100;
                }
                // if($itemsTotal == 0 || $itemsCount == 0){
                //     $totalAverage = 0;
                // }
                // else{
                //     $totalAverage = $itemsTotal/$itemsCount;
                // }
            }

            //$itemsTotal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT itemId FROM userItems INNER JOIN items ON userItems.itemId = items.item_id WHERE userId = $uId"));
            //$itemsCount = 
        }
    }
    else{
        header("location: http://brookes-sems.epizy.com/login/");
        exit();
    }
?>
            
<!DOCTYPE html>

<html>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    

    <style>
        img.icon{
            width: auto;
            height: 1.5em;
        }

        #dashboard{
            width: 70%;
            min-height: 100vh;
            display: block;
            background-color: var(--light-gray, lightgray);
        }

        #details{
            width: 30%;
            min-height: 100vh;
            display: block;
            position: fixed;
            top: 0;
            right: 0;
            background-color: var(--white, white);
            text-align: center;
        }

        #content{
            background-color: var(--light-gray, lightgray);
        }

        #profile-image-container{
            display: inline-block;
            height: auto;
            width: 50%;
            position: relative;
            border-radius: 50%;
            overflow: hidden;
            border: 5px solid var(--light-gray, lightgray);
        }

        #profile-image-container:before{
            content:"";
            display: block;
            width: 100%;
            padding-bottom: 100%;
            position: relative;
            z-index: -1;
        }

        #profile-image{
            display: block;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            object-fit: cover;
        }
    </style>

    <head>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <link rel="stylesheet" href="//brookes-sems.epizy.com/styles/colors.css">
        <link rel="stylesheet" href="//brookes-sems.epizy.com/styles/main.css">
        <link rel = "stylesheet" href = "//brookes-sems.epizy.com/styles/slider.css">
        <script src="/scripts/cookies.js"></script>
        <script src="/scripts/dark-mode.js"></script>
        <script>
            $(document).ready(function(){
                var ctx = document.getElementById("myChart").getContext("2d");
                var itemsPieChart = new Chart(ctx,{
                    type:'pie',
                    data: {
                        datasets: [{
                            data: [20, 20, 20],    //insert user data
                            backgroundColor: ["#0074D9", "#FF4136", "#2ECC40", "#FF851B", "#7FDBFF", "#B10DC9", "#FFDC00", "#001f3f", "#39CCCC", "#01FF70", "#85144b", "#F012BE", "#3D9970", "#111111", "#AAAAAA"]
                        }],
                        labels: ['1', '2', '3'] //insert labels for data
                    },
                    options: {
                        responsive: 'true',
                    }
                })
                ctx = document.getElementById("myChart2").getContext("2d");
                var barChart = new Chart(ctx,{
                    type:'bar', //change to line graph to show spending over months
                    data: {
                        datasets: [{
                            data: [20, 20, 20],  //insert user data
                            backgroundColor: ["#0074D9", "#FF4136", "#2ECC40", "#FF851B", "#7FDBFF", "#B10DC9", "#FFDC00", "#001f3f", "#39CCCC", "#01FF70", "#85144b", "#F012BE", "#3D9970", "#111111", "#AAAAAA"]
                        }],
                        labels: ['1', '2', '3'] //insert labels for data
                    },
                    options: {
                        responsive: 'true',
                        scales: {
                            yAxes: [{
                                gridLines: {
                                    zeroLineColor: '#000000'
                                },
                                ticks: {
                                    fontColor: '#000000',
                                    tickColor: '#000000',
                                    tickWidth: 2
                                }
                            }],
                            xAxes: [{
                                gridLines: {
                                    zeroLineColor: '#000000'
                                },
                                ticks: {
                                    fontColor: '#000000',
                                    tickColor: '#000000',
                                    tickWidth: 2
                                }
                            }]
                        }
                    }
                });
                const c = new Cookie();
                if(c.isSet('sems-dark-mode') && c.get('sems-dark-mode').toLowerCase() === 'true' || window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) document.getElementById('dark-mode-toggle').checked = true;
            });
        </script>
        
        <title>Profile</title>
    </head>
    <body>
        <div id = "content">
            <div id = "dashboard">
                <h1> My Profile </h1>
                <canvas id = "myChart"></canvas>
                <canvas id = "myChart2"></canvas>
            </div>
            <div id = "details">
                <div id = "profile-image-container">
                    <img id = "profile-image" src = <?=$userData["profileImg"]?> alt = "Profile picture"/> <!-- $userData["profilePicture"]; -->
                </div>
                <h2><?=$userData["firstName"];?> <?=$userData["lastName"];?></h2>
                <h4><?=$userData["userUni"];?>: <?=$userData["userCourse"];?></h4>
                <h4>Spent: &pound;<?=$userData["monthlyOutgoing"];?> | Budget: &pound;<?=$userData["monthlyBudget"];?></h4>
                <h4>Spent: <?=$balacePercentage;?>%</h4>
                <h4>Average Spend: &pound;</h4>
                <a href = "http://brookes-sems.epizy.com/settings"><h4><img class = icon src="settings icon.png" alt="Settings"/>Account Settings</h4></a>
                <a href = "http://brookes-sems.epizy.com/privacy"><h4><img class = icon src="privacy icon.png" alt="Privacy"/>Privacy Settings</h4></a>
                <a href = "http://brookes-sems.epizy.com/friends-list"><h4><img class = icon src="friends icon.png" alt="Friends"/>Friends</h4></a>
                <h4><img class = icon src="dark mode icon.png" alt="Dark mode"/></a>Dark Mode</h4>
                <label class="switch">
                    <input id="dark-mode-toggle" type="checkbox" onchange="window.toggleDarkMode()">
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </body>
</html>