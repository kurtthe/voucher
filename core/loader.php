<?php

session_start();

//$fileName	=	end(explode("/", $_SERVER['SCRIPT_FILENAME']));

//$allowedFilesArray = array('login.php', 'register.php', 'do_login.php');

//if($_SESSION['admin']==''){
// header("Location:login.php");
//}

require_once("config/global.config.php");

require_once("config/tables.config.php");

require_once("functions/database.php");

require_once("functions/string.php");

require_once("functions/messages.php");

require_once("functions/html.php");

require_once("functions/FlashMessages.php");

// Instantiate the flash message class
$flash = new FlashMessages();
?>