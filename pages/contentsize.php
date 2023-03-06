<?php
//ВЫБОР КОЛИЧЕСТВА ОТОБРАЖАЕМЫХ СТРОК ТАБЛИЦЫ
	session_start();

	switch ($_GET['selsize']) {
		case '0':
			$_SESSION['selsize']=5;
			break;
		case '1':
			$_SESSION['selsize']=15;
			break;
		case '2':
			$_SESSION['selsize']=30;
			break;
		default:
			
			break;
	}
	header("Location: ../index.php");