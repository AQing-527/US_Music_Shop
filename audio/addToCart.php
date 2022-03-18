<?php
session_start();

$username = $_POST['username'];
$musicId = $_POST['musicId'];
$quantity = $_POST['quantity'];
$cartId = 0;

$conn = mysqli_connect('sophia.cs.hku.hk','xqchen2','cxqcxq27','xqchen2') or die ("Connection error.".mysqli_connect_error());
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

?>