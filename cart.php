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
    <title>US MUSIC SHOP - CART</title>
    <link rel="stylesheet" href="css/cart.css">
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
      <div id="contentWrapper">
      <div id="logoutdiv"></div>
      <div id="title"><h1>My Shopping Cart</h1></div>
      <div id="contents"></div>
      <div id="pricediv"></div>
      <div id="bttns">
          <button id="back">Back</button>
          <button id="checkout">Checkout</button>
      </div>
    </div>

    <script>
      var username;

      <?php
      if (isset($_SESSION['username'])) {
        print "username = '" . $_SESSION['username'] . "';";
      } else { ?>
          var cartNum = <?php print $_SESSION['cartNum']; ?> ; 
          var cart = <?php echo json_encode($_SESSION['cart']); ?>;
      <?php } ?>


      window.onload = function(){
        showAll();
      }

      document.getElementById("search").addEventListener('click', function(){
            var keyword = document.getElementById("keyword").value;
            if (keyword != ""){
              window.location.href = `main.php?search='${keyword}'`;
            }
        })

      contentdiv = document.getElementById("contents");
      var totalPrice=0;

      function showAll(){
        updateCartNum();
        contentdiv.innerHTML = "";
        <?php if(isset($_SESSION['username'])){?>
        fetch(`query.php?get=cart&user='${username}'`).then(response => {
            response.json().then( data => {
            if (data.length==0){
              let pricediv = document.getElementById("pricediv");
              pricediv.innerHTML="";
            }
            for (var i = 0; i< data.length; i++) {
                var m = data[i];
                totalPrice+=m["Quantity"]*m["Price"];
                updatePrice();
                let box = document.createElement("div");
                box.setAttribute("class", "musicbox");
                
                let textbox = document.createElement("div");
                textbox.setAttribute("class", "textbox");
                let detail = document.createElement("h3");
                detail.setAttribute("class", "detail");
                let output = "";
                output+="Music Name: " + m["MusicName"] + "<br>";
                output+="Quantity: " + m["Quantity"] + "<br>";
                detail.innerHTML = output;
                textbox.appendChild(detail);
                box.appendChild(textbox);

                let deleteBttn = document.createElement("button");
                deleteBttn.setAttribute("class", "deleteBttns");
                deleteBttn.setAttribute('onclick', `deleteItem(${m["MusicId"]});`);
                deleteBttn.innerHTML="Delete";
                box.appendChild(deleteBttn);
                contentdiv.appendChild(box);
          }
        })
        })
          <?php }else{ ?>
          if(cart.length == 0){
            let pricediv = document.getElementById("pricediv");
            pricediv.innerHTML="";
          }
          for (var i=0; i<cart.length; i++ ){
            totalPrice+=cart[i][2]*cart[i][3];
            updatePrice();
            
            let box = document.createElement("div");
            box.setAttribute("class", "musicbox");
                
            let textbox = document.createElement("div");
            textbox.setAttribute("class", "textbox");
            let detail = document.createElement("h3");
            detail.setAttribute("class", "detail");
            let output = "";
            output+="Music Name: " + `${cart[i][1]}` + "<br>";
            output+="Quantity: " + `${cart[i][2]}` + "<br>";
            detail.innerHTML = output;
            textbox.appendChild(detail);
            box.appendChild(textbox);

            let deleteBttn = document.createElement("button");
            deleteBttn.setAttribute("class", "deleteBttns");
            deleteBttn.setAttribute('onclick', `deleteItem(${cart[i][0]});`);
            deleteBttn.innerHTML="Delete";
            box.appendChild(deleteBttn);
            contentdiv.appendChild(box);
          }
          <?php } ?> //end of else statement
        
      }

      function updatePrice(){
          let pricediv = document.getElementById("pricediv");
          pricediv.innerHTML="";
          let price = document.createElement("h3");
          price.setAttribute("id", "price");
          price.innerHTML = "Total Price: $ " +totalPrice;
          pricediv.appendChild(price);
        
      }

      function deleteItem(mid) {
        <?php if(isset($_SESSION['username'])){ ?>
          fetch(`query.php?delete=cart&user='${username}'&range=${mid}`).then(response => {
          totalPrice=0;
          showAll();
          }
          )
          updateCartNum();
        <?php } else{ ?>
          fetch(`query.php?delete=cart&range=${mid}`).then(response => { response.json().then( data => {
          cartNum = data[0];
          cart = data[1];
          totalPrice=0;
          showAll();
          })
          })
          updateCartNum();
        <?php } ?> //end of else statement
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

          <?php }else{ ?>
            let cart = document.getElementById("cart");
            cart.innerHTML="Cart "+ cartNum;
          <?php } ?>
    }

        document.getElementById("back").addEventListener('click', function() {
                window.location.href = "main.php";
            })
        
        document.getElementById("checkout").addEventListener('click', function() {
                window.location.href = "checkout.php";
            })
        

        <?php
        if (isset($_SESSION['username'])) {
        ?>
          document.getElementById("logout").addEventListener('click', function() {
            let logoutdiv = document.getElementById("logoutdiv");
            logoutdiv.innerHTML="";
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