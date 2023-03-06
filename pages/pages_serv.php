<?php
//ВЫБОР СТРАНИЦЫ
session_start();
$_SESSION['page'] = $_POST['page'];
header("Location: ../index.php");
