<?php
$servername = 'localhost';
$username = 'root';
$password = 'mysql';
$datab = 'netology4_2';
try {
    $db = new PDO("mysql:host=$servername;dbname=$datab", $username, $password);
//    $db = new PDO('mysql:host=localhost;dbname=rmakarov;charset=utf8', 'rmakarov', 'neto1741');
    $db -> exec("SET CHARACTER SET utf8");
    // set the PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//    echo 'Connected successfully';
} catch(PDOException $e)
{
    echo 'Connection failed: ' . $e->getMessage();
    die();
}