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
    <title>US MUSIC SHOP - CHECK OUT</title>
    <link rel="stylesheet" href="css/checkout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
      <div id="contentWrapper">
      <div id="logindiv">
      </div>
      <div id="delivery">
        <div id="creatediv"></div>
        <div><h2>Delivery Address:</h2></div>
        <div>
        <p class="labels">Full Name</p>
        <input type="text" name="fullName" placeholder="Required" class="textField" required><br>
        </div>
        <div>
        <p class="labels">Company Name</p>
        <input type="text" name="companyName" class="textField"><br>
        </div>
        <div>
        <p class="labels">Address Line 1</p>
        <input type="text" name="address1" placeholder="Required" class="textField" required><br>
        </div>
        <div>
        <p class="labels">Address Line 2</p>
        <input type="text" name="address2" class="textField"><br>
        </div>
        <div>
        <p class="labels">City</p>
        <input type="text" name="city" placeholder="Required" class="textField" required><br>
        </div>
        <div>
        <p class="labels">Region/State/District</p>
        <input type="text" name="region" class="textField"><br>
        </div>
        <div>
        <p class="labels">Country</p>
        <input type="text" name="country" placeholder="Required" class="textField" required><br>
        </div>
        <div>
        <p class="labels">Postcode/Zip Code</p>
        <input type="text" name="postcode" placeholder="Required" class="textField" required><br>
        </div>
      </div>
      <h3 id="warning">Please do not leave the required fields empty!</h3>

        <div id="lowerdiv">
        <div id="order">
            <h3>Your order: ( <a href="cart.php">change</a> )</h3>
        </div>
        <h3>Free Standard Shipping</h3>
        <div id="info">
            
        </div>
        <div id="pricediv"></div>
        </div>
        <div id="bttns">
          <button id="confirm">Confirm</button>
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

      contentdiv = document.getElementById("info");
      var totalPrice=0;

      <?php if(!isset($_SESSION["username"])){ ?>
        let logindiv = document.getElementById("logindiv");
        logindiv.innerHTML = "";
        logindiv.style = "display:block";
        logindiv.innerHTML += "<h2>I'm a new customer</h2>";
        logindiv.innerHTML += "<h4>Please Checkout Below</h4>";
        logindiv.innerHTML += "<h3>or</h3>";
        logindiv.innerHTML += "<h2>I'm already a customer</h2>";
        logindiv.innerHTML += "<h4><a id='login'>Sign In</a></h4>";

        let creatediv = document.getElementById("creatediv");
        creatediv.innerHTML = "";
        creatediv.innerHTML += "<h2>Create Account:</h2>";
        creatediv.innerHTML += "<div><p class='labels'>Username</p><input type='text' id='username' name='username' placeholder='Desired Username' class='textField' required><br></div>";
        creatediv.innerHTML += "<div id='duplicate'></div>";
        creatediv.innerHTML += "<div><p class='labels'>Password</p><input type='password' name='password' placeholder='Desired Password' class='textField' required><br></div>";
      <?php } else {?>
        let logindiv = document.getElementById("logindiv");
        logindiv.innerHTML = "";
        logindiv.style = "display:none";
        let creatediv = document.getElementById("creatediv");
        creatediv.innerHTML = "";
      <?php } ?>

      function showAll(){
        <?php if (isset($_SESSION['username'])){ ?>
        contentdiv.innerHTML = "";
        fetch(`query.php?get=cart&user='${username}'`).then(response => {
            response.json().then( data => {
            for (var i = 0; i< data.length; i++) {
                var m = data[i];
                totalPrice+=m["Quantity"]*m["Price"];
                updatePrice();
                let box = document.createElement("div");
                box.setAttribute("class", "musicbox");
                
                let textbox = document.createElement("div");
                textbox.setAttribute("class", "textbox");
                let detail = document.createElement("p");
                detail.setAttribute("class", "detail");
                let output = "";
                output+= m["Quantity"] + " x " + m["MusicName"] + " HK$ " + m["Quantity"]*m["Price"]+"<br>";
                detail.innerHTML = output;
                textbox.appendChild(detail);
                box.appendChild(textbox);

                contentdiv.appendChild(box);
          }
        })
        })
        <?php } else { ?>
          contentdiv.innerHTML = "";
          for (var i = 0; i< cart.length; i++) {
                totalPrice+=cart[i][2]*cart[i][3];
                updatePrice();
                let box = document.createElement("div");
                box.setAttribute("class", "musicbox");
                
                let textbox = document.createElement("div");
                textbox.setAttribute("class", "textbox");
                let detail = document.createElement("p");
                detail.setAttribute("class", "detail");
                let output = "";
                output+= cart[i][2] + " x " + cart[i][1] + " HK$ " + cart[i][2]*cart[i][3]+"<br>";
                detail.innerHTML = output;
                textbox.appendChild(detail);
                box.appendChild(textbox);

                contentdiv.appendChild(box);
            }



        <?php } ?> //end of else statement
        
      }

      function updatePrice(){
        let pricediv = document.getElementById("pricediv");
        pricediv.innerHTML="";
        let price = document.createElement("h3");
        price.setAttribute("id", "price");
        price.innerHTML = "Total Price: HK$ " +totalPrice;
        pricediv.appendChild(price);
      }

    //handle the click of the confirm button
    document.getElementById("confirm").addEventListener('click', function() {
            //check whether inputs are valid
                <?php if(! isset($_SESSION['username'])){ ?>
                if (document.querySelectorAll("input")[0].value == "" || document.querySelectorAll("input")[1].value == "" || document.querySelectorAll("input")[2].value == "" || document.querySelectorAll("input")[4].value == "" || document.querySelectorAll("input")[6].value == ""|| document.querySelectorAll("input")[8].value == ""|| document.querySelectorAll("input")[9].value == "") {
                document.getElementById("warning").innerHTML = "Please do not leave the required fields empty!";
                document.getElementById("warning").style = "display:block";
                } 
                else {
                document.getElementById("warning").style = "display:none";
                var xhr = new XMLHttpRequest();

                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        var response = xhr.responseText;
                        if (response == "success") { //successfully create an account
                            
                        }
                        else { //user already exists
                           
                        }
                    }
                }

                xhr.open("POST","create.php",true);
                xhr.setRequestHeader("content-type","application/x-www-form-urlencoded");
                xhr.send("username="+document.querySelectorAll("input")[0].value+"&password="+document.querySelectorAll("input")[1].value)
                
                window.location.href = "invoice.php?fullName=" + document.querySelectorAll("input")[2].value + 
                        "&companyName=" + document.querySelectorAll("input")[3].value +
                        "&address1=" + document.querySelectorAll("input")[4].value +
                        "&address2=" + document.querySelectorAll("input")[5].value +
                        "&city=" + document.querySelectorAll("input")[6].value +
                        "&region=" + document.querySelectorAll("input")[7].value +
                        "&country=" + document.querySelectorAll("input")[8].value +
                        "&postcode=" + document.querySelectorAll("input")[9].value;
                }
            
            
            <?php }else { ?>
              if (document.querySelectorAll("input")[0].value == "" || document.querySelectorAll("input")[2].value == "" || document.querySelectorAll("input")[4].value == "" || document.querySelectorAll("input")[6].value == "" || document.querySelectorAll("input")[7].value == "") {
                document.getElementById("warning").innerHTML = "Please do not leave the required fields empty!";
                document.getElementById("warning").style = "display:block";
              } else {
                document.getElementById("warning").style = "display:none";
                window.location.href = "invoice.php?fullName=" + document.querySelectorAll("input")[0].value + 
                        "&companyName=" + document.querySelectorAll("input")[1].value +
                        "&address1=" + document.querySelectorAll("input")[2].value +
                        "&address2=" + document.querySelectorAll("input")[3].value +
                        "&city=" + document.querySelectorAll("input")[4].value +
                        "&region=" + document.querySelectorAll("input")[5].value +
                        "&country=" + document.querySelectorAll("input")[6].value +
                        "&postcode=" + document.querySelectorAll("input")[7].value;
            }
            <?php } ?>
        }
        )

        <?php if(!isset($_SESSION['username'])){?>
          document.getElementById("login").addEventListener('click', function() {window.location.href = "login.php"});
          
          document.getElementById("username").addEventListener('blur', function() 
            { 
              let errordiv = document.getElementById("duplicate");
              fetch(`query.php?check='${document.getElementById('username').value}'`).then(response => {
                response.json().then(data=>{
                  if(data.length!=0){
                    errordiv.innerHTML="<p>Username Duplicated!</p>";
                    document.getElementById("username").value='';
                  }
                  else{
                    errordiv.innerHTML="";
                  }
                })
            })
            });        
        <?php } ?>

    </script>
</body>