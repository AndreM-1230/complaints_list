<?php
session_start();
include('env.php');
include('functions.php');
$id =  array_keys($_POST)[0];
$rating = $_POST[$id];
sqltab("UPDATE complaints_list SET rating = $rating WHERE id = $id");
header('Location:index.php');