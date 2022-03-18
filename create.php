<?php
session_start();

$username = $_POST['username'];
$password = $_POST['password'];

$conn = mysqli_connect('sophia.cs.hku.hk','xqchen2','cxqcxq27','xqchen2') or die ("Connection error.".mysqli_connect_error());
$result = mysqli_query($conn,'select * from login where UserId = "'.$username.'";') or die ("Query error.".mysqli_error($conn));
if(mysqli_num_rows($result)==0){
    print "success";
    $query='insert into login(UserId, PW) values ("'.$username.'","'.$password.'");';
    $add=mysqli_query($conn,$query) or die("Fail to add data. ".mysqli_error($conn));
}
else{
    print "wrong";
}

mysqli_free_result($result);
mysqli_close($conn);

?>