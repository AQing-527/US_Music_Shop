<?php
session_start();

if ($_GET["get"]){
    if ($_GET["get"] == "music") {
        if ($_GET["range"]=="all"){
            getAll();
        }
        else{
            getDetail();
        }
    }
    
    else if ($_GET["get"] == "cart"){
        if ($_GET["num"]){
            getCartNum();
        }
        else{
            getCart();
        }

    }
}

else if($_GET["delete"]){
    if ($_GET["delete"] == "cart"){
        if ($_GET["user"]){
            if ($_GET["range"] == "all"){
                deleteCart();
            }
            else{
                deleteCartItem();
            }
        }
        else{
            if ($_GET["range"] == "all"){
                deleteCartFromSession();
            }
            else{
                deleteCartItemFromSession();
            }
        }

    }
}

else if($_GET["check"]){
    checkuser();
}



function getAll()
{
    $conn = mysqli_connect('sophia.cs.hku.hk', 'xqchen2', 'cxqcxq27', 'xqchen2') or die("Connection error." . mysqli_connect_error($conn));

    $result = mysqli_query($conn, 'select * from music') or die("Query error." . mysqli_error($conn));
    $json_str = json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
    print $json_str;

    mysqli_free_result($result);
    mysqli_close($conn);

}

function getDetail()
{
    $conn = mysqli_connect('sophia.cs.hku.hk', 'xqchen2', 'cxqcxq27', 'xqchen2') or die("Connection error." . mysqli_connect_error($conn));

    $result = mysqli_query($conn, 'select * from music where MusicId = '.$_GET["range"]) or die("Query error." . mysqli_error($conn));
    $json_str = json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
    print $json_str;

    mysqli_free_result($result);
    mysqli_close($conn);
}

function getCart(){
    $conn = mysqli_connect('sophia.cs.hku.hk', 'xqchen2', 'cxqcxq27', 'xqchen2') or die("Connection error." . mysqli_connect_error($conn));

    $result = mysqli_query($conn,'select M.MusicId, M.MusicName, sum(C.Quantity) as Quantity, M.Price from music M, cart C where C.UserId='.$_GET["user"].'and M.MusicId=C.MusicId group by M.MusicId') or die("Query error." . mysqli_error($conn));

    $json_str = json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
    print $json_str;

    mysqli_free_result($result);
    mysqli_close($conn);
}

function getCartNum(){
    $conn = mysqli_connect('sophia.cs.hku.hk', 'xqchen2', 'cxqcxq27', 'xqchen2') or die("Connection error." . mysqli_connect_error($conn));

    $result = mysqli_query($conn,'select sum(Quantity) as Quantity from cart where UserId='.$_GET["user"]) or die("Query error." . mysqli_error($conn));

    $json_str = json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
    print $json_str;

    mysqli_free_result($result);
    mysqli_close($conn);

}

function deleteCart(){
    $conn = mysqli_connect('sophia.cs.hku.hk', 'xqchen2', 'cxqcxq27', 'xqchen2') or die("Connection error." . mysqli_connect_error($conn));

    $result = mysqli_query($conn,'delete from cart where UserId='.$_GET["user"].';') or die("Query error." . mysqli_error($conn));

    $json_str = json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
    print $json_str;

    mysqli_free_result($result);
    mysqli_close($conn);

}

function deleteCartItem(){
    $conn = mysqli_connect('sophia.cs.hku.hk', 'xqchen2', 'cxqcxq27', 'xqchen2') or die("Connection error." . mysqli_connect_error($conn));

    $result = mysqli_query($conn,'delete from cart where UserId='.$_GET["user"].' and MusicId='.$_GET["range"].';') or die("Query error." . mysqli_error($conn));

    $json_str = json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
    print $json_str;

    mysqli_free_result($result);
    mysqli_close($conn);

}

function deleteCartFromSession(){
    unset($_SESSION['cart']);
    unset($_SESSION['cartNum']);
}

function deleteCartItemFromSession(){
    $max=sizeof($_SESSION['cart']);
    for($i=0; $i<$max; $i++){
        if ($_SESSION['cart'][$i][0] == $_GET["range"]){
            $_SESSION['cartNum']-=$_SESSION['cart'][$i][2];
            unset($_SESSION['cart'][$i]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
        }
    }
    $json_str = json_encode(array($_SESSION['cartNum'], $_SESSION['cart']));
    print $json_str;
}

function checkuser(){
    $conn = mysqli_connect('sophia.cs.hku.hk', 'xqchen2', 'cxqcxq27', 'xqchen2') or die("Connection error." . mysqli_connect_error($conn));

    $result = mysqli_query($conn,'select UserId from login where UserId='.$_GET["check"].';') or die("Query error." . mysqli_error($conn));

    $json_str = json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
    print $json_str;

    mysqli_free_result($result);
    mysqli_close($conn);
}

?>