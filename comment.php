<?php
session_start();
include('env.php');
include('functions.php');
$text = $_POST['text'];
$id =  intval($_POST['data_id']);
sqltab("UPDATE complaints_list SET status = 3, fix_comment = '$text' WHERE id = $id");
header('Location:index.php');