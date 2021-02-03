<?php 

function LogUser()
{
	if(isset($_SESSION["random"]) &&
	   $_SESSION["random"] != $_POST["random"]) return null;

	global $db;
	
	$login = trim(htmlspecialchars($_POST["login"]));
	$password = htmlspecialchars($_POST["password"]);		
	
	if(!$login)
	{
		$_POST["password"] = NULL;				
		return "Логин не введён!";	
	}
	else if(!$password) return "Пароль не введён!";		
	
	$SQL = "SELECT id_user, Login, Password, Level FROM Users WHERE Login = '$login'";
	
	if(!$result = mysqli_query($db, $SQL)) return "Ошибка: ".mysqli_error($db);	
	
	if(mysqli_num_rows($result) == 0)
	{
		$_POST["login"] = NULL;
		$_POST["password"] = NULL;
		return "Пользователь не существует.";	
	}
			
	$row = mysqli_fetch_assoc($result);
	
	if(!password_verify($password, $row["Password"]))
	{
		$_POST["password"] = NULL;
		return "Неправильный пароль!";	
	}
	
	unset($_SESSION["id_guest"]);
	unset($_SESSION["tab_order"]);
	unset($_SESSION["random"]);	
	$_SESSION["id_user"] = $row["id_user"];		
	
	return TRUE;	
	
}

function ShowContent()
{
	if(isset($_SESSION["id_user"]))
	{
		print "<div id='wrapper-content'>";
		print "<a href='exit.php'>Выход</a>";
		
		ShowTabs();	
		
		ShowForm("log-tabform");
	
		print "</div>";	
		 
	}
	else
	{
		print "<div id='wrapper'>
			   	<div id='guest-section'>		 					
						<p id='guest-p'>
							Здесь вы можете добавить <span style='color: yellow'>визуальные закладки</span> для нужных сайтов.<br><br>								   
							Зарегистрируйтесь, чтобы добавлять неограниченное число закладок!
						</p>";				
		
		ShowLogForm();
		ShowRegForm();
		
		print "</div>";

		ShowGuestTabs();		
	}
	
	if(isset($_SESSION["err"])) $_SESSION["err"] = null;
	
}

function ShowLogForm()
{
	global $log_res;

	$random = microtime();
	$_SESSION["random"] = $random;
		
	print "<div id='forms-block'>";	
		
	if($log_res && $log_res !== TRUE) print "<span id='log-err'>$log_res</span>";
				
?>			
		<h1>Вход</h1>
		
		<form action="index.php" method="POST">
			
			<p>Логин<input type="text" name="login" maxlength=30 size=15
										 <?php if(isset($_POST["login"])) print "value='".$_POST["login"]."'"; ?>></p>
			<p>Пароль<input type="password" name="password" maxlength=30 size=15 autocomplete="off"
										 <?php if(isset($_POST["password"])) print "value='".$_POST["password"]."'"; ?>></p>
			<p><input id="log-button" type="submit" name="log_button" value="Войти"></p>
			<input type="hidden" name="random" <?php print "value='$random'" ?>>
			
		</form>			
		<a href="">Регистрация</a>
		
<?php

}

function ShowRegForm()
{
	global $reg_res;

	$random = $_SESSION["random"];

	if($reg_res === true)
	{
		$msg = "Успешная регистрация!";
		print "<div id='reg-success'>
				   <i class='far fa-check-circle'></i>				   
				   <span>$msg</span>				   
			   </div>";
	}      
	else if($reg_res !== null) print "<span id='reg-err'>$reg_res</span>";
?>
		<h1 class="hidden">Регистрация</h1>

		<form action="index.php" method="post" class="hidden">
			<p>
				<label for="ul">Логин</label><br>
				<input name="user_login" id="ul" type="text"
				<?php if(isset($_POST["user_login"])) print "value='".$_POST["user_login"]."'"; ?>>
			</p>
			<p><label>Пароль<br><input name="user_password" type="password" autocomplete="off"
									  <?php if(isset($_POST["user_password"])) print "value='".$_POST["user_password"]."'"; ?>></label></p>
			<p><label>Повторите пароль<br><input name="password_again" type="password" autocomplete="off"></label></p>
			<p><input id="reg-button" name = "register" type="submit" value="Зарегистрироваться"></p>
			<input type="hidden" name="random" <?php print "value='$random'" ?>>
		 </form>
		 <a href="" class="hidden">Вход</a>
	 </div>
<?php	
	
}

function RegUser()
{
	if(isset($_SESSION["random"]) &&
	   $_SESSION["random"] != $_POST["random"]) return null;

	global $db;
	// Проверка данных	
	
	if(!$_POST['user_login'])
	{		
		$_POST['user_password'] = NULL;
		return "Не указан логин";
	}	
	else if(!$_POST['user_password'])
	{
		$_POST['user_password'] = NULL;
		return "Не указан пароль";
	}
	else if($_POST['user_password'] != $_POST['password_again'])
	{
		$_POST['user_password'] = NULL;
		return "Введённые пароли не совпадают!";
	}
	     
	$login = trim($_POST["user_login"]);	
	
	// Проверяем не зарегистрирован ли уже пользователь
	$SQL = "SELECT `Login` FROM `Users` WHERE `Login` LIKE '$login'";
	
	if ($result = mysqli_query($db, $SQL)) 
	{		
		if(mysqli_num_rows($result) > 0) return "Пользователь с указанным логином уже зарегистрирован.";		
	}
	else
	{
		printf("Ошибка: %s\n", mysqli_error($db));
	} 
	// Если такого пользователя нет, регистрируем его
	$hash_pass = password_hash($_POST['user_password'], PASSWORD_DEFAULT);
	
	$SQL = "INSERT INTO `Users`(`Login`,`Password`, `Level`) VALUES 
			('$login', '$hash_pass', 1)";

	if (!mysqli_query($db, $SQL))
	{
		printf("Ошибка: %s\n", mysqli_error($db));
		return FALSE;
	}

	return TRUE;
	
}

function ShowTabs()
{	
	global $db;
	
	$id = $_SESSION["id_user"];
	
	$SQL = "SELECT * FROM Tabs WHERE id_user = $id";	
		
	if(!($result = mysqli_query($db, $SQL)))
	{
		 print "Ошибка: ".mysqli_error($db)."<br><br>";
		 return;
	}
	
	if(mysqli_num_rows($result) > 0)
	{
		print "<div id='content'>";	

		while($row = mysqli_fetch_assoc($result))
		{
			print "<div class='tab'>";
			print "<div class='tab_block-img'><a href='".$row["URL"]."' target='_blank'><img src='".$row["Screenshot"]."'></a></div>";	
			print "<div class='redcross-block'><a href='delete.php/?id=".$row["id_tab"]."'><img src='src/rc.png'></a></div>";
			print "</div>";			
		}

		print "</div>";

		mysqli_free_result($result);
	}
	
}

function ShowTabContainers($i = 0, $single = false)
{
	for($i; $i < 3; $i++)
	{
		print "<a class='tab-container' data-item=$i>+";
		
		if(isset($_SESSION["err"]) && $_SESSION["err_order"] == $i)
		{
			print "<span id='err'>".$_SESSION["err"]."</span>";			
		} 
		print "</a>";
		
		if($single) break;
	}
}

function ShowGuestTabs()
{
	$i = 0;

	print '<div id="guest-content-section">
		   	<h2>Попробуйте...</h2>
	   		<p>Нажмите на "+", введите адрес сайта, нажмите "добавить закладку" и дождитесь окончания небольшой загрузки.</p>';
	
	print '<div id="guest-content">';			   
	
	if(isset($_SESSION["id_guest"]))
	{
		global $db;
		$id = $_SESSION["id_guest"];
		
		$SQL = "SELECT * FROM Tabs WHERE id_user = $id";
		
		if(!$result = mysqli_query($db, $SQL)) print "Ошибка: ".mysqli_error($db)."<br><br>";

		if(mysqli_num_rows($result) > 0)
		{
			$row = mysqli_fetch_all($result, MYSQLI_ASSOC);					
		
			for($i; $i < 3; $i++)
			{				
				if(isset($_SESSION["tab_order"][$i]))
				{
					foreach($row as $arr)
					{						
						if($arr["id_tab"] == $_SESSION["tab_order"][$i])
						{							
							print "<div class='guest-tab'>
										<a href='".$arr["URL"]."' target='_blank'><img src='".$arr["Screenshot"]."'></a>
										<a href='delete.php/?id=".$arr["id_tab"]."&order=$i'><img src='src/rc.png'></a>
								  </div>";							
							
							break;
						}				
						
					}
				}
				else ShowTabContainers($i, true);				
				
			}
			
			mysqli_free_result($result);			
		}
		else ShowTabContainers($i);			
				
	}
	else ShowTabContainers();
	
	ShowForm();

	print "</div></div>";
	
}

function GetSiteTitle($url)
{
	if(!$site = file_get_contents($url)) 
	{		
		$last_error = error_get_last();
		
		if((strpos($last_error["message"], "403") !== FALSE) OR
		   (strpos($last_error["message"], "405") !== FALSE)) return "Без названия";
		else
		{
			$_SESSION["err"] = "Сайт не найден!";
			return FALSE;
		}
		
	}
	
	if(mb_detect_encoding($site, "UTF-8", TRUE) != "UTF-8") return "Без названия";	
	
	if((preg_match("/<title>(.+)<\/title>/", $site, $res)) != 1) return "Без названия";
		
	$title = $res[1];
	
	$title = trim($title);
	
	return $title;
	
}

function GetSiteScreenshot($url)
{
	$dir = "pic/";
	$resolution = "1280x1024";
	$size = 350;
	$format = "png";	
	$cur_time = time(); 	
	
	$screenshot = md5($cur_time.$url.$resolution.$size).".$format";		
	
	$scr_url = "https://mini.s-shot.ru/".$resolution."/".$size."/".$format."/?".$url;

	if(!($scr_string = file_get_contents($scr_url)))
	{
		$_SESSION["err"] = "Попробуйте позже";
		return FALSE;
	}

	$screenshot_new = fopen($dir.$screenshot, "w+");
	fwrite($screenshot_new, $scr_string);
	fclose($screenshot_new);
	
	return $dir.$screenshot;
	
}

function ShowForm($tabform = "tabform")
{	
	 print "<form id='".$tabform."' action='addtab.php' method='POST'>";
	 if($tabform == "log-tabform")
	 { 					
?>	 
	<label for="url">Адрес сайта:</label>
	<input id="url" type="text" name="url" maxlength=2048 size=30 placeholder="Минимум 3 символа">
	<input type="submit" name="addtab" value="Добавить закладку" disabled>
<?php
	 }
	 else
	 {
?>	 
	 <a><img src="src/rc.png"></a>
	 <label for="url">Адрес сайта:</label><br>
	 <input type="text" name="url" maxlength=2048 size=30 placeholder="Минимум 3 символа"><br><br>
	 <input type="submit" name="addtab" value="Добавить закладку" disabled>
	 <input type="hidden" id="input-order" name="order">
<?php
	 }

	if(isset($_SESSION["err"]) && $tabform == "log-tabform")
	{
		print "<span id='log-tabform_err'>".$_SESSION["err"]."</span>";				
	}

	print "</form>";	
}

?>
