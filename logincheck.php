<?php
session_start();
function yeslogin()
{
    if($_SESSION['username'] != null){
        die("<script>window.location.pathname ='index.php'</script>");
    }
}

function nologin()
{
    if($_SESSION['username'] == null){
        die("<script>alert('当前未登录，自动跳转到index.php');window.location.pathname ='login.php'</script>");
    }
}
?>