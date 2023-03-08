<?php
var_dump($_POST);
session_start();
include('env.php');
include('functions.php');
$text = $_POST['text'];
$id =  number_format($_POST['data_id']);
echo $id;
sqltab("UPDATE complaints_list SET fix_comment = $text WHERE id = $id");
header('Location:index.php');