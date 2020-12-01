<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reset Password - SEMS</title>
        
        <link rel="stylesheet" href="//brookes-sems.epizy.com/styles/main.css">
        
        <script src="//brookes-sems.epizy.com/scripts/menu.js" type="text/javascript"></script>
        
        <script>
            
            function resetPassword(){
                var x;
                if(window.XMLHttpRequest){
                    x = new XMLHttpRequest();
                }
                else{
                    x = new ActiveXObject("Microsoft.XMLHTTP");
                }
                var form = document.getElementById('user-pr-form');
                x.onreadystatechange = function(){
                    if(this.readyState == 4){
                        for(let responseElem of document.querySelectorAll('form#user-pr-form p.response')){
                            if(responseElem.hasAttribute('success')) responseElem.removeAttribute('success');
                            switch(this.status){
                                case 200:
                                    form.reset();
                                    responseElem.setAttribute('success', '');
                                default:
                                    responseElem.innerText = JSON.parse(this.responseText).response.message;
                            }
                        }
                    }
                };
                x.open("POST", "http://brookes-sems.epizy.com/password-reset/reset.php", true);
                var formdata = new FormData(form);
                x.send(formdata);
            }
            
        </script>
        <style>
            
            form#user-pr-form {
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
            <div id="pr-area" class="section">
                <form id="user-pr-form" method="POST" action="reset.php" onsubmit="event.preventDefault();resetPassword();return false;">
                    <?php
                        if(!isset($_GET['u']) || empty($_GET['u']) || !isset($_GET['i']) || empty($_GET['i'])){
                            echo <<<EOT
                                <h1>Uh oh!</h1>
                                <h2>Reset your password.</h2>
                                <p1>Enter the email address registered with your account.</p1>
                                <p3>If you typed your email address correctly, you will receive a password reset link.</p3>
                                <br />
                                <p class="response"></p>
                                <label for="user-pr-email-input">Email address</label>
                                <input id="user-pr-email-input" type="text" name="email" placeholder="Email address" />
                                <br />
                                <input type="submit" value="Send Reset Email" />
EOT;
                        }
                        else{
                            echo <<<EOT
                                <h2>Change your password.</h2>
                                <p1>Update your password to a new password.</p1>
                                <br />
                                <p class="response"></p>
                                <label for="user-pr-password-input">Password</label>
                                <input id="user-pr-password-input" type="password" name="password" placeholder="Password" />
                                <label for="user-pr-cpassword-input">Confirm password</label>
                                <input id="user-pr-cpassword-input" type="password" name="cpassword" placeholder="Confirm password" onkeyup="if(this.value != document.getElementById('user-pr-password-input').value){this.classList.add('invalid');}else{this.classList.remove('invalid');}" />
                                <input type="hidden" name="hash" value="{$_GET['u']}" />
                                <input type="hidden" name="uid" value="{$_GET['i']}" />
                                <br />
                                <input type="submit" value="Change Password" />
EOT;
                        }
                    ?>
                </form>
            </div>
        </div>
    </body>
</html>