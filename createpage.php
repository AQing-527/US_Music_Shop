<?php
session_start();
if (!isset($_SESSION['username'])){
    if (!isset($_SESSION['cartNum'])){
      $_SESSION['cartNum']=0;
      $_SESSION['cart'] = array();
    }
  }
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>US MUSIC SHOP - CREATE ACCOUNT</title>
    <link rel="stylesheet" href="css/loginAndCreate.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
<header>
        <div id="leftdiv">
            <input type="text" id="keyword" name="keyword" placeholder="Keyword(s)">
            <button id="search">Search</button>
        </div>
        <div id="rightdiv">
            <?php
          if (isset($_SESSION['username'])) {
          ?>
                <button id="logout">Log out</button>
                <button id="cart">Cart</button>
                <?php
          } else {
          ?>
                    <button id="login">Sign in</button>
                    <button id="register">Register</button>
                    <button id="cart">Cart</button>
                    <?php
          }
          ?>
        </div>
    </header>
    <div id="frame">
        <h3 id="title">US MUSIC SHOP - CREATE ACCOUNT</h3>
        <div>
            <i class="fa fa-user icon"></i>
            <input type="text" name="username" placeholder="Desired Username" class="textField" required>
        </div>

        <div>
            <i class="fa fa-key icon"></i>
            <input type="password" name="password" placeholder="Desired Password" class="textField" required>
        </div>
        
        <h1 id="warning">Please do not leave the fields empty!</h1>
        <div id="bttns">
            <button id="confirm">CONFIRM</button>
            <button id="back">BACK</button>
        </div>
    </div>

    <div id="imgbox">
        <img src="img/logo.png">
    </div>

    
    <script>
        var cartNum;
        document.getElementById("back").addEventListener('click', function () {
            window.location.replace("login.php");
        })

        document.getElementById("search").addEventListener('click', function(){
            var keyword = document.getElementById("keyword").value;
            if (keyword != ""){
              window.location.href = `main.php?search='${keyword}'`;
            }
        })

        <?php print "cartNum = '" . $_SESSION['cartNum'] . "';";?>
        let cart = document.getElementById("cart");
        cart.innerHTML="Cart "+ cartNum;

        //handle the click of the sumbit button
        document.getElementById("confirm").addEventListener('click', function () {
            //check whether inputs are valid
            if (document.querySelectorAll("input")[1].value == "" || document.querySelectorAll("input")[2].value == "") {
                document.getElementById("warning").innerHTML = "Please do not leave the fields empty!";
                document.getElementById("warning").style = "display:block";
            }
            else {
                document.getElementById("warning").style = "display:none";

                var xhr = new XMLHttpRequest();

                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        var response = xhr.responseText;
                        if (response == "success") { //successfully create an account
                            document.getElementById("warning").innerHTML = "Account created! Welcome!";
                            document.getElementById("warning").style = "display:block";
                            setTimeout(function () { 
                                window.location.replace("login.php"); // the redirect goes here
                            },3000); // 3 seconds
                        }
                        else { //user already exists
                            document.getElementById("warning").innerHTML = "Account already existed.";
                            document.getElementById("warning").style = "display:block";
                            setTimeout(function () { 
                                window.location.replace("createpage.php"); // the redirect goes here
                            },3000); // 3 seconds
                        }
                    }
                }

                xhr.open("POST","create.php",true);
                xhr.setRequestHeader("content-type","application/x-www-form-urlencoded");
                xhr.send("username="+document.querySelectorAll("input")[1].value+"&password="+document.querySelectorAll("input")[2].value)
            }
        })
        <?php
    if (isset($_SESSION['username'])) {
    ?>
      document.getElementById("logout").addEventListener('click', function() {
        window.location.replace("logout.php");
      });
      document.getElementById("cart").addEventListener('click', function() {
        window.location.href = "cart.php";
      });
    <?php
    } else {
    ?>
      document.getElementById("login").addEventListener('click', function() {
        window.location.href = "login.php";
      });
      document.getElementById("register").addEventListener('click', function() {
        window.location.href = "createpage.php";
      });
      document.getElementById("cart").addEventListener('click', function() {
        window.location.href = "cart.php";
      });
    <?php
    }
    ?>
    </script>
</body>