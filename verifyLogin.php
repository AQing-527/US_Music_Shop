<?php
session_start();

$username = $_POST['username'];
$password = $_POST['password'];

$conn = mysqli_connect('sophia.cs.hku.hk','xqchen2','cxqcxq27','xqchen2') or die ("Connection error.".mysqli_connect_error());

$result = mysqli_query($conn,'select * from login where UserId = "'.$username.'";') or die ("Query error.".mysqli_error($conn));
if(mysqli_num_rows($result)==0){
    print "unregistered";
}
else{
    $row=mysqli_fetch_array($result);
    if($row['PW']==$password){
        print "success";
        $_SESSION['username']=$row['UserId'];
        for($i=0; $i<sizeof($_SESSION['cart']); $i++){
            $result = mysqli_query($conn,'select max(CartId) as CartId from cart;') or die ("Query error.".mysqli_error($conn));
            if(mysqli_num_rows($result)==0){
                $cartId = 1;
            }
            else{
                $row=mysqli_fetch_array($result);
                $cartId = $row['CartId']+1;
            }//get cartId
            $query='insert into cart(CartId, MusicId, UserId, Quantity) values ("'.$cartId.'","'.$_SESSION['cart'][$i][0].'","'.$username.'","'.$_SESSION['cart'][$i][2].'");';
            $add=mysqli_query($conn,$query) or die("Fail to add data. ".mysqli_error($conn));
        }
        unset($_SESSION['cart']);
        unset($_SESSION['cartNum']);
    }
    else{
        print "wrong";
    }
}

mysqli_free_result($result);
mysqli_close($conn);

?>