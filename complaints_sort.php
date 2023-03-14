<?php
session_start();
$_SESSION['complaints_sort'] = match ($_GET['admin_sort']) {
    '1' => 1,
    '2' => 2,
    '3' => 3,
    '4' => 4,
    default => 1,
};
header('Location:index.php');