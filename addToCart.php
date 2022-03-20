<?php
session_start();

if (isset($_SESSION['username'])){
    $username = $_POST['username'];
    $musicId = $_POST['musicId'];
    $quantity = $_POST['quantity'];
    $cartId = 0;

    $conn = mysqli_connect(hostname, username, password,database) or die ("Connection error.".mysqli_connect_error());
    $result = mysqli_query($conn,'select max(CartId) as CartId from cart;') or die ("Query error.".mysqli_error($conn));
    if(mysqli_num_rows($result)==0){
        $cartId = 1;
    }
    else{
        $row=mysqli_fetch_array($result);
        $cartId = $row['CartId']+1;
    }

    $query='insert into cart(CartId, MusicId, UserId, Quantity) values ("'.$cartId.'","'.$musicId.'","'.$username.'","'.$quantity.'");';
    $add=mysqli_query($conn,$query) or die("Fail to add data. ".mysqli_error($conn));
    print "success";

    mysqli_free_result($result);
    mysqli_close($conn);
}
else{
    $musicId = $_POST['musicId'];
    $musicName = $_POST['musicName'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    $max=sizeof($_SESSION['cart']);
    for ($i=0; $i<$max; $i++){
        if ($_SESSION['cart'][$i][0] == $musicId){
            $exist = true;
            $_SESSION['cart'][$i][2] += $quantity;
            $_SESSION['cartNum'] += $quantity;
            break;
        }
    }
    if(!$exist){
        $b=array($musicId, $musicName, $quantity, $price);
        // $b=array("MusicID"=>'$musicId',"MusicName"=>$musicName, "Quantity"=>$quantity, "Price"=>$price);
        array_push($_SESSION['cart'], $b );
        $_SESSION['cartNum'] += $quantity;
    }
    print "success";
}
?>