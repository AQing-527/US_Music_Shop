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
    <title>US MUSIC SHOP</title>
    <link rel="stylesheet" href="css/musicinfo.css">
</head>

<body>
    <header>
    <div id="leftdiv">
      <div id="directory">
      <u><a id="home" onclick="goHome();">Home</a></u>
    </div>
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

    <div id="contents"> 
    </div>
    <div id="order">
    <label>Order: </label>
    <input type="number" id="num" name="num" value="1" min="1">
    <button id="add">Add to Cart</button>
    <p id="warning"></p>
    </div>



    <script>
        var mid = <?php print $_GET['mid'] ?>;
        var username;
        var contentdiv = document.getElementById("contents");
        <?php
        if (isset($_SESSION['username'])) {
            print "username = '" . $_SESSION['username'] . "';";
        }
        ?>
        var music;
        var price;
        var cartNum;


        window.onload = getInfo;

        document.getElementById("search").addEventListener('click', function(){
            var keyword = document.getElementById("keyword").value;
            if (keyword != ""){
              window.location.href = `main.php?search='${keyword}'`;
            }
        })

        <?php if (isset($_SESSION['username'])){?>
        //handle the click of the sumbit button
        document.getElementById("add").addEventListener('click', function (){
          //check whether input is valid
          if (document.querySelectorAll("input")[1].value=="" || document.querySelectorAll("input")[1].value%1!=0 || document.querySelectorAll("input")[1].value<1 || document.querySelectorAll("input")[1].value>9999) {
                document.getElementById("warning").innerHTML = "Please enter a positive integer! (1-9999)";
                document.getElementById("warning").style = "display:block";
            }
            else {
                document.getElementById("warning").style = "display:none";

                var xhr = new XMLHttpRequest();

                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        var response = xhr.responseText;
                        if (response == "success") { //successfully create an account
                          window.location.href = "cart.php";
                        }
                    }
                }
                xhr.open("POST","addToCart.php",true);
                xhr.setRequestHeader("content-type","application/x-www-form-urlencoded");
                xhr.send("username="+username+"&musicId="+mid+"&quantity="+document.querySelectorAll("input")[1].value);
        
              }
        })
        <?php } else{ ?>
        //handle the click of the sumbit button
        document.getElementById("add").addEventListener('click', function (){
          //check whether input is valid
           if (document.querySelectorAll("input")[1].value=="" || document.querySelectorAll("input")[1].value%1!=0 || document.querySelectorAll("input")[1].value<1 || document.querySelectorAll("input")[1].value>9999) {
                 document.getElementById("warning").innerHTML = "Please enter a positive integer! (1-9999)";
                 document.getElementById("warning").style = "display:block";
             }
             else {
                 document.getElementById("warning").style = "display:none";

                 var xhr = new XMLHttpRequest();

                 xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                         var response = xhr.responseText;
                         if (response == "success") { //successfully create an account
                            window.location.href = "cart.php";
                         }
                     }
                 }
                 xhr.open("POST","addToCart.php",true);
                 xhr.setRequestHeader("content-type","application/x-www-form-urlencoded");
                 xhr.send("musicId="+mid+"&musicName="+musicName+"&quantity="+document.querySelectorAll("input")[1].value+"&price="+price);
                }
              })
        <?php } ?>


        function getInfo() {
          updateCartNum();
            //Get the music
            fetch("query.php?get=music&range=" + mid).then(response => {
                response.json().then(quesHandler);
            })

            function quesHandler(data) {
                var m = data[0];
                contentdiv.innerHTML = "";
                var logoutdiv = document.createElement("div");
                logoutdiv.id = "logoutdiv";
                contentdiv.appendChild(logoutdiv);
                let box = document.createElement("div");
                directory = document.getElementById("directory");
                directory.innerHTML = "";
                directory.innerHTML = `<u><a id='home' onclick='goHome();'>Home</a></u><p id="arrow">></P><u><a id='musicD'>${m["MusicName"]}</a></u>`;
                box.setAttribute("class", "musicbox");
                
                let titlebox = document.createElement("div");
                titlebox.setAttribute("class", "titlebox");
                
                let title = document.createElement("h3");
                title.innerHTML = m["MusicName"];
                musicName = m["MusicName"];
                price = m["Price"];
                title.setAttribute("class", "musictitle");
                titlebox.appendChild(title);
                box.appendChild(titlebox);
                contentdiv.appendChild(box);

                let imagebox = document.createElement("div");
                imagebox.setAttribute("class", "imagebox");
                let image = new Image;
                image.src = "img/img_"+(mid).toString()+".jpg";
                box.appendChild(imagebox);
                image.onload =  function(event){
                    checksize(event);
                    imagebox.appendChild(image);
                };
                

                let audiobox = document.createElement("div");
                audiobox.setAttribute("class", "audiobox");
                audiobox.innerHTML= `<audio autoplay controls><source src="audio/m${mid}.mp3" type="audio/mpeg"></audio>`;
                box.appendChild(audiobox);

                let textbox = document.createElement("div");
                textbox.setAttribute("class", "textbox");
                let detail = document.createElement("p");
                detail.setAttribute("class", "detail");
                let output = "";
                output+="Composer: " + m["Composer"] + "<br>";
                output+="Published: " + m["Published"] + "<br>";
                output+="Category: " + m["Category"] + "<br>";
                output+="Description: " + m["Description"] + "<br>";
                output+="<span style='font-weight:bold'> Price: $ " + m["Price"] + "</span><br>";
                detail.innerHTML = output;
                textbox.appendChild(detail);
                box.appendChild(textbox);
                }
        }

        function checksize(event){
            event.target.height = 300;
            // if (event.target.naturalHeight == 0){
            //     event.target.height = 260;
            //     return 0;
            // }
            // {
            // if (event.target.naturalWidth / event.target.naturalHeight >= 2/3){
            //     event.target.height = 260 * event.target.naturalHeight/event.target.naturalWidth;
            // }
            // else{
            //     event.target.width = 390 * event.target.naturalWidth / event.target.naturalHeight;
            // }}
        }

        function goHome(){
            window.location.href = "main.php";
        }

        function updateCartNum(){
      <?php if(isset($_SESSION['username'])){ ?>
            fetch(`query.php?get=cart&user='${username}'&num=yes`).then(response => {
            response.json().then( data => {
              cartnum = data[0];
              let cart = document.getElementById("cart");
              if (cartnum['Quantity']!=null){
                cart.innerHTML="Cart "+ cartnum['Quantity'];

              }
              else{
                cart.innerHTML="Cart 0";
              }
            })})

          <?php }else{ 
            print "cartNum = '" . $_SESSION['cartNum'] . "';";?>
            let cart = document.getElementById("cart");
            cart.innerHTML="Cart "+ cartNum;
          <?php } ?>
    }

        <?php
        if (isset($_SESSION['username'])) {
        ?>
          document.getElementById("logout").addEventListener('click', function() {
            let logoutdiv = document.getElementById("logoutdiv");
            logoutdiv.innerHTML = "";
            let logoutMsg = document.createElement("h2");
            logoutMsg.setAttribute("id", "logoutMsg");
            logoutMsg.innerHTML = "Logging out";
            logoutdiv.appendChild(logoutMsg);
            setTimeout(function() {
              window.location.replace("logout.php"); // the redirect goes here
            }, 3000); // 3 seconds
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