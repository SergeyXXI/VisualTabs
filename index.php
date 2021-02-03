<?php session_start(); require "header.php"; StartDB(); 

if(isset($_POST["log_button"])) $log_res = LogUser(); 
if(isset($_POST["register"]))   $reg_res = RegUser();
	
ShowContent();			
		
EndDB(); require "footer.php"; 

?>
