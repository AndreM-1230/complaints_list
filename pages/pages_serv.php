<?php
//ВЫБОР СТРАНИЦЫ
    session_start();
    $_SESSION['page'] = number_format($_POST['page'], 0);
    header("Location: ../comment_list/index.php");
