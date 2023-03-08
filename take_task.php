<?php
session_start();
include('env.php');
include('functions.php');
// array_keys($_POST)[0] - id строки;
$id =  array_keys($_POST)[0];
$fixer = $_SESSION['account'][0]['id'];
sqltab("UPDATE complaints_list SET admin = $fixer WHERE id = $id");
header('Location:index.php');
