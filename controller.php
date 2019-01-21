<?php
include_once './model/function.php';
$login = !empty($_SESSION['login']) ? $_SESSION['login'] : false;
// Проверка на авторизацию
if (empty($login)) {
    include_once './template/login.php';
    die;
} else {
    include_once './template/home.php';
}