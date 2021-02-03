<?php session_start(); require "start_mysql.php"; require "main.php"; StartDB();

if(isset($_POST["order"])) $_SESSION["err_order"] = $_POST["order"];

$url = htmlspecialchars($_POST["url"]);
$url = str_replace(" ", "", $url);

if(($pos = strpos($url, "http://")) !== 0)
{
	if(($pos = strpos($url, "https://")) !== 0) $url = "https://".$url;
}

if(!($title = GetSiteTitle($url)))
{
	header("Location: index.php");
	EndDB();
	return;
}

if(!($screenshot = GetSiteScreenshot($url)))
{
	header("Location: index.php");
	EndDB();
	return;
}
	
if(isset($_SESSION["id_user"])) 	  $id = $_SESSION["id_user"];
else if(isset($_SESSION["id_guest"])) $id = $_SESSION["id_guest"];		 
else
{		
	$login = "guest - ".date("d.m.Y,H:i:s");
	
	$SQL = "INSERT INTO Users(`Login`) VALUES('$login')";
	
	if(!mysqli_query($db, $SQL))
	{
		print "Ошибка добавления гостя: ".mysqli_error($db)."<br><br>";
		EndDB();
		return;
	}
	
	$_SESSION["id_guest"] = mysqli_insert_id($db);
	$id = $_SESSION["id_guest"];	
}
		
$SQL = "INSERT INTO Tabs(`Title`, `URL`, `Screenshot`, `id_user`) VALUES('$title', '$url', '$screenshot', $id)";

if(!mysqli_query($db, $SQL))
{
	print "Ошибка добавления закладки: ".mysqli_error($db)."<br><br>";
	EndDB();
}
else
{
	if(isset($_POST["order"]))
	{
		$order = $_POST["order"];
		$_SESSION["tab_order"][$order] = mysqli_insert_id($db);			
				
	}                           
	EndDB();	
	header("Location: index.php");
}
		 
?>
