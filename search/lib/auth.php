<?php
session_start();
include('../config.php');
$pass = $_POST['password'];

if(strlen(PASS) > 0 && $pass == PASS) {
    $_SESSION['session_id'] = md5($pass);
}

header('Location: http://'. $_SERVER['HTTP_HOST'] . str_replace('lib/auth.php', '', $_SERVER['REQUEST_URI']));
?>
