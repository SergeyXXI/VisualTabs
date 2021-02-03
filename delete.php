<?php

session_start(); require "start_mysql.php"; 

StartDB();

$id = $_GET["id"];
$i = $_GET["order"];

$SQL = "SELECT Screenshot FROM Tabs WHERE id_tab = $id";

$result = mysqli_query($db, $SQL);
	
$row = mysqli_fetch_assoc($result);

$file = $row["Screenshot"];	
unlink($file);	

mysqli_free_result($result);

$SQL = "DELETE FROM Tabs WHERE id_tab = $id";

if(!mysqli_query($db, $SQL)) print "Ошибка: ".mysqli_error($db)."<br><br>";

$_SESSION["tab_order"][$i] = null;

EndDB();

header("Location: ".$_SERVER["HTTP_REFERER"]);	


?>
