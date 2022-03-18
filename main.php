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
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <header>
    <div id="leftdiv">
      <div id="directory">
      <u><a id="home" onclick="showAll();">Home</a></u>
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
  <div id="afterHeader">
  <div id="logoutdiv"></div>
  <div id="categoryWrapper">
        <ul id="tags">
        <li><h2>Category</h2></li>
        <?php
        $conn = mysqli_connect('sophia.cs.hku.hk','xqchen2','cxqcxq27','xqchen2') or die ("Connection error.".mysqli_connect_error());
        $result = mysqli_query($conn,'select distinct Category from music;') or die ("Query error.".mysqli_error($conn));
        ?>
        <?php
        while ($row=mysqli_fetch_array($result)){
            print "<li><a class='category'>".$row['Category']."</a></li>";
        }
        mysqli_free_result($result);
        mysqli_close($conn);
        ?>
        </ul></div>
   <div id="contentWrapper">
        <div id="headingdiv">
        <h2 id="contentHeading">All Music</h2>
        </div>
        <div id="contents">
    </div>
  </div>
      </div>
  <script>
    var username;
    var imageIdx = [];
    var showing;
    var cartnum=0;
    var cartNum;
    var category;
    var contentdiv = document.getElementById("contents");
    var categoryList = document.getElementsByClassName('category');
    for (var i=0; i<categoryList.length; i++){
      // console.log(categoryList[i].innerHTML);
      var str = categoryList[i].innerHTML.toString();
      categoryList[i].setAttribute("onclick", `filter('${str}')`);
    }

    function goHome(){
      window.location.href = "main.php";
    }

    <?php
    if (isset($_SESSION['username'])) {
      print "username = '" . $_SESSION['username'] . "';";
    }
    ?>
    
    window.onload = function(){
      <?php if ($_GET['search']){
      ?>
      var keyword = <?php print $_GET['search'] ?>;
      document.getElementById("keyword").value = keyword;
      search();
      <?php
      } else { ?>
      showAll();
      <?php } ?>
    }

    document.getElementById("search").addEventListener('click', search);


    function showAll() {
      <?php if(! $_GET['category']){ ?>
      directory = document.getElementById("directory");
      directory.innerHTML = "";
      directory.innerHTML = "<u><a id='home' onclick='goHome();'>Home</a>";
        document.getElementById("contentHeading").innerHTML = "All Music";
        document.getElementById("keyword").value="";
        imageIdx = [];
        fetch("query.php?get=music&range=all").then(response => {
            response.json().then( music => {
            for (var i=0; i<music.length; i++){
                imageIdx.push(i);
            }
            generate(music);
        })
        })
        <?php }else { 
          print "category = '" . $_GET['category'] . "';";?>
           document.getElementById("contentHeading").innerHTML = `All ${category}`;
           imageIdx=[];
           directory = document.getElementById("directory");
           directory.innerHTML = "";
           directory.innerHTML = `<u><a id='home' onclick='goHome();'>Home</a></u><p id="arrow">></P><u><a id='categoryD'>${category}</a></u>`;

             fetch("query.php?get=music&range=all").then(response => {
                 response.json().then( music => {
                 let filterMusic = [];
                 for (var i = 0; i< music.length; i++){
                   if (music[i].Category==category){
                         filterMusic.push(music[i]);
                         imageIdx.push(i);
                   }
                 }
                 generate(filterMusic);
        })
        })
         <?php } ?>
    }
    
    function search(){
        var keyword = document.getElementById("keyword").value;
        if (keyword != ""){
          document.getElementById("contentHeading").innerHTML = "Searching Results";
          directory = document.getElementById("directory");
          directory.innerHTML = "";
          directory.innerHTML = `<u><a id='home' onclick='goHome();'>Home</a></u>`;
          var keywordList = keyword.split(" ");
          imageIdx=[];
          
          fetch("query.php?get=music&range=all").then(response => {
              response.json().then( music => {
              let searchMusic = [];
              for (var i = 0; i< music.length; i++){
                for (var j=0; j<keywordList.length; j++){
                  if ((music[i].MusicName).indexOf(keywordList[j]) !== -1 || (music[i].Composer).indexOf(keywordList[j]) !== -1){
                      searchMusic.push(music[i]);
                      imageIdx.push(i);
                      break;
                  }
                }
              }
              generate(searchMusic);
      })
      })
      }   
    }

    function filter(category){
      window.location.href = "main.php?category=" + category;
    }

    
    function generate(data) {
      updateCartNum();
      document.getElementById("logoutdiv").innerHTML = "";
        contentdiv.innerHTML = "";
        for (var i = 0; i< data.length; i++) {
            var m = data[i];
            let box = document.createElement("div");
            box.setAttribute("class", "musicbox");
            
            let titlebox = document.createElement("div");
            titlebox.setAttribute("class", "titlebox");
            
            let title = document.createElement("a");
            title.setAttribute("class", "musictitle");
            let musicId = m["MusicId"].toString();
            title.setAttribute("onclick", `toDetail('${musicId}')`);
            title.innerHTML = m["MusicName"];
            titlebox.appendChild(title);
            box.appendChild(titlebox);
            contentdiv.appendChild(box);

            let imagebox = document.createElement("div");
            imagebox.setAttribute("class", "imagebox");
            let image = new Image;
            image.src = "img/img_"+(imageIdx[i]+1).toString()+".jpg";
            box.appendChild(imagebox);
            image.onload =  function(event){
                checksize(event);
                imagebox.appendChild(image);
            };

            let textbox = document.createElement("div");
            textbox.setAttribute("class", "textbox");
            let detail = document.createElement("p");
            detail.setAttribute("class", "detail");
            let output = "";
            if (m["NewArrival"]=="Yes"){
                output+= "<span style='color:red; font-weight:bold'>NEW ARRIVAL!</span><br>";
            }
            output+="Composer: " + m["Composer"] + "<br>";
            output+="<span style='font-weight:bold'> Price: $ " + m["Price"] + "</span><br>";
            detail.innerHTML = output;
            textbox.appendChild(detail);
            box.appendChild(textbox);
      }
    }

    function checksize(event){
    if (event.target.naturalHeight == 0){
        event.target.height = 200;
        return 0;
    }
    {
    if (event.target.naturalWidth / event.target.naturalHeight >= 2/3){
        event.target.height = 200 * event.target.naturalHeight/event.target.naturalWidth;
    }
    else{
        event.target.width = 300 * event.target.naturalWidth / event.target.naturalHeight;
    }}

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

    function toDetail(mid) {
      window.location.href = "musicinfo.php?mid=" + mid;
    }

    
  </script>
</body>