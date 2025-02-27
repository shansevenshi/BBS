<?php
session_start();
$username="root";
$password="123456";
$host="localhost";
$port=3306;
$dbname="mybbs";
$conn=mysqli_connect($host,$username,$password,$dbname,$port);
if($conn->connect_error){
    die("连接数据库失败".$conn->connect_error);
}
$conn->query('set names utf8');
