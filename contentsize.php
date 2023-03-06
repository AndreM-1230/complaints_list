<?php
//ВЫБОР КОЛИЧЕСТВА ОТОБРАЖАЕМЫХ СТРОК ТАБЛИЦЫ
	session_start();
	$_SESSION["newsession"]='';

	switch ($_GET['selsize']) {
		case '0':
			$_SESSION['selsize']=20;
			break;
		case '1':
			$_SESSION['selsize']=40;
			break;
		case '2':
			$_SESSION['selsize']=60;
			break;
		default:
			
			break;
	}
	header("Location: index.php");