<?php

error_reporting(1);

ini_set("display_errors", 1);

// Global Configuration File

/***********************Database Credentials*****************************/

if(!defined('DATABASE_HOST')){define('DATABASE_HOST', 'localhost');}
if(!defined('DATABASE_USER')){define('DATABASE_USER', 'root');}
if(!defined('DATABASE_PASS')){define('DATABASE_PASS', 'control123');}
#if(!defined('DATABASE_PASS')){define('DATABASE_PASS', 'setarehsahar2');}
if(!defined('DATABASE_NAME')){define('DATABASE_NAME', 'u157-gvms-db');}
if(!defined('TABLE_PREFIX')){define('TABLE_PREFIX', 'gvms_tb_');}

/***********************URI Settings*****************************/
define('UPLOAD_LOGO_DIR', '/uploads/voucher_logo/');
define('UPLOAD_OUTLETLOGO_DIR', '/uploads/outlet_logo/');

/**********************Environment Settings******************************/

if(!defined('ENVIRONMENT')){define('ENVIRONMENT', 0);}		//0=Development or 1=Live

/**********************Base Url******************************/
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
define('BASE_URL', $protocol.$_SERVER['HTTP_HOST']);

/***********************Error Handling*****************************/

error_reporting(0);

ini_set("display_errors", 0);

if(intval(ENVIRONMENT) == 1){
	
	error_reporting(0);

	ini_set("display_errors", 0);
	
}else{
	
	error_reporting(E_ALL);

	ini_set("display_errors", 1);
	
}

?>