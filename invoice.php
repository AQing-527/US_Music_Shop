<?php
session_start();

$fullName = strval($_GET['fullName']);
if ($_GET['companyName'] == ""){
    $companyName = "NA";
}
else{
    $companyName = $_GET['companyName'];
}
$address1 = $_GET['address1'];
if ($_GET['address2'] == ""){
    $address2 = "NA";
}
else{
    $address2 = $_GET['address2'];
}
$city = $_GET['city'];
if ($_GET['region'] == ""){
    $region = "NA";
}
else{
    $region = $_GET['region'];
}
$country = $_GET['country'];
$postcode = $_GET['postcode'];

?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>US MUSIC SHOP - INVOICE</title>
    <link rel="stylesheet" href="css/invoice.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
      <div id="contentWrapper">
      <div id="invoice">
        <h2>Invoice Page</h2>
        <p class="labels"><b>Full Name:</b> <?php print $fullName; ?></p><br>
        <p class="labels"><b>Company Name:</b> <?php print $companyName; ?></p><br>
        <p class="labels"><b>Address Line 1:</b> <?php print $address1; ?></p><br>
        <p class="labels"><b>Address Line 2:</b> <?php print $address2; ?></p><br>
        <p class="labels"><b>City:</b> <?php print $city; ?></p><br>
        <p class="labels"><b>Region/State/District:</b> <?php print $region; ?></p><br>
        <p class="labels"><b>Country:</b> <?php print $country; ?></p><br>
        <p class="labels"><b>Postcode/Zip Code:</b> <?php print $postcode; ?></p><br>
        </div>

        <div id="lowerdiv">
        <div id="info">
            
        </div>
        <div id="pricediv"></div>
        </div>
        <h4>Thanks for ordering. Your music will be delivered within 7 working days.</h4>
        <div id="bttns">
          <button id="OK">OK</button>
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

    //handle the click of the OK button
    document.getElementById("OK").addEventListener('click', function() {
        <?php if(isset($_SESSION['username'])) { ?>
        
        //delete the contents in cart
        fetch(`query.php?delete=cart&user='${username}'&range=all`).then(response => {
        })
        window.location.replace("main.php");

        <?php } else { ?>
        fetch(`query.php?delete=cart&range=all`).then(response => {
        })
        window.location.replace("main.php");
        
        <?php } ?> // end of else statement;
      })
    </script>
</body>