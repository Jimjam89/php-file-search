<?php
session_start();
include('config.php');
$pass = $_POST['password'];

if($pass == PASS) {
    $_SESSION['session_id'] = md5($pass);
}

header('Location: http://'. $_SERVER['HTTP_HOST'] . str_replace('auth.php', '', $_SERVER['REQUEST_URI']));
?>
