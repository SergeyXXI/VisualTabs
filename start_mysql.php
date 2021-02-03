<?php

$db;
	
function StartDB()
{
	global $db;
	
	if(!$db = mysqli_connect("localhost", "root", "root", "test")) print "Ошибка: ".mysqli_connect_error()."<br><br>";
	
	mysqli_set_charset($db, "UTF8");
		
}

function EndDB()
{
	global $db;
	
	if(!mysqli_close($db)) print "Ошибка закрытия БД";
		
}

function InitDbTables()
{
	global $db;
	
	$SQL = "DROP TABLE IF EXISTS Users";
			
	
	if(mysqli_query($db, $SQL)) print "Таблица Users удалена!<br><br>";
	else                        print "Ошибка: ".mysqli_error($db)."<br><br>";
	
	
	$SQL = "CREATE TABLE Users(`id_user` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
							   `Login` VARCHAR(30) NOT NULL,
							   `Password` VARCHAR(255),
							   `Level` INT DEFAULT 0 NOT NULL,
							   `Registration` TIMESTAMP)";
								 
	if(mysqli_query($db, $SQL)) print "Таблица Users создана!<br><br>";
	else                        print "Ошибка: ".mysqli_error($db)."<br><br>";	
	
	$hash_admin = password_hash("0", PASSWORD_DEFAULT);	
	
	$SQL = "INSERT INTO Users(`Login`, `Password`, `Level`) VALUES('admin', '$hash_admin', 10)";															 
																	 
    if(mysqli_query($db, $SQL)) print "Админ создан!<br><br>";
	else                        print "Ошибка: ".mysqli_error($db)."<br><br>";	
																 
	
	$SQL = "DROP TABLE IF EXISTS Tabs";			
	
	if(mysqli_query($db, $SQL)) print "Таблица Tabs удалена!<br><br>";
	else                        print "Ошибка: ".mysqli_error($db)."<br><br>";
	
	$SQL = "CREATE TABLE Tabs(`id_tab` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
							  `Title` VARCHAR(256) NOT NULL,
							  `URL` VARCHAR(2048) NOT NULL,
							  `Screenshot` VARCHAR(64) NOT NULL,
							  `id_user` INT NOT NULL)";
								 
	if(mysqli_query($db, $SQL)) print "Таблица Tabs создана!<br><br>";
	else                        print "Ошибка: ".mysqli_error($db)."<br><br>";	
	
}


?>
