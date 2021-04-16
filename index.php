<?php
    require_once('scripts/user.php');
    $user = new User();
    if(!$user->isLoggedIn()){
        header("location: //brookes-sems.epizy.com/login");
        exit();
    }
    else{

    }
?>

<!DOCTYPE html>
<html>
<head>

    <title>SEMS</title>

    <link rel="stylesheet" href="../styles/main.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="//brookes-sems.epizy.com/scripts/menu.js" type="text/javascript"></script>
    <script src="/scripts/transactions.js"></script>

    <style>

        div#transaction-area {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            width: 100%;
            height: auto;
        }

    </style>

</head>
<body>
<menu-bar></menu-bar>
<div id="content">
    <div class="section">
        <div class="post">
            <h1>Recents.</h1>
            <h3>Most Recent Transactions.</h3>
            <div id="transaction-area">
                <transactions-list></transactions-list>
            </div>
        </div>
    </div>
</div>
</body>
</html>