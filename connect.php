<?php
	// подключение файла с переменными для подключения к БД
	require("config.php");
	// подключение к базе данных
	$mysqli = @new mysqli ($dbhost, $dbuser, $dbpassword, $dbname);
	
	// установка кодировки
	@$mysqli->set_charset("utf8");

	// проверка на установку соединения
	if($mysqli->connect_errno) {
		die("Ошибка соединения: ". $mysqli->connect_errno . ".<br />Описание ошибки: " .  $mysqli->connect_error);
	}