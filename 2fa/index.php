<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>2FA - SEMS</title>
        
        <link rel="stylesheet" href="//brookes-sems.epizy.com/styles/main.css">
        
        <script src="//brookes-sems.epizy.com/scripts/menu.js" type="text/javascript"></script>
        
        <script>
            
            function 2FAreset(){
                var x;
                if(window.XMLHttpRequest){
                    x = new XMLHttpRequest();
                }
                else{
                    x = new ActiveXObject("Microsoft.XMLHTTP");
                }
                x.onreadystatechange = function(){
                    if(this.readyState == 4){
                        console.log(this.responseText);
                        for(let responseElem of document.querySelectorAll('form#user-pr-form p.response')){
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
                x.open("POST", "http://brookes-sems.epizy.com/2FA/2FAreset.php", true);
                var form = document.getElementById('user-pr-form');
                const formdata = new FormData(form);
                x.send(formdata);
            }
            
        </script>
        <style>
            
            form#user-2fa-form {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: flex-start;
                align-self: center;
                padding: 10px;
                border-radius: 10px;
                width: 30%;
                max-width: 300px;
            }
            
        </style>
    </head>
    <body>
        <menu-bar></menu-bar>
        <div id="content">
            <div id="2fa-area" class="section">
                <form id="user-2fa-form" method="POST" action="2FAreset.php" onsubmit="event.preventDefault();2FAreset();return false;">
                    <?php
                        if(!isset($_GET['u']) || empty($_GET['u']) || !isset($_GET['i']) || empty($_GET['i'])){
                            echo <<<EOT
                                <h1>Just a quick check</h1>
                                <p1>A code was just sent to your email account.</p1>
                                <p3>Type in the code sent to you to access your account.</p3>
                                <br />
                                <p class="response"></p>
                                <label for="user-2fa-code-input">Code sent to you</label>
                                <input id="user-2fa-code-input" type="text" name="2FACode" placeholder="" />
                                <br />
                                <input type="submit" value="Validate 2FA Code" />
EOT;
                        }
                    ?>
                </form>
            </div>
        </div>
    </body>
</html>