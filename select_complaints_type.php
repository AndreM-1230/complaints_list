<?php
session_start();
var_dump($_GET);
if($_GET['com_sel'] == '1'){
    //Все записи
    $_SESSION['complaints_type'] = 1;
}
else{
    //Записи пользователя
    $_SESSION['complaints_type'] = 2;
}
header('Location:index.php');