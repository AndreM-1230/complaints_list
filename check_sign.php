<?php
session_start();
imagine_load();
if($_SESSION['account'] == null){
    include('env.php');
    include('functions.php');
    $login = $_POST['login'];
    $password = $_POST['password'];
    $user = sqltab("SELECT * FROM users
             WHERE 
            (login = '$_POST[login]') and
            (password = '$_POST[password]')");
    var_dump($_POST);
    var_dump($user);
    $_SESSION['account'] = $user;
}
else{
    $_SESSION['account'] = null;
}
header('Location: index.php');



