<?php
$Document = substr($_SERVER["DOCUMENT_ROOT"], 0, strrpos($_SERVER["DOCUMENT_ROOT"], "/"));
//$Document = substr($Document, 0, strrpos($Document, "/"));
//include($Document ."/config.php");
include("/var/www/html/txpay/config.php");

//$Document = "/home/txpay/ag/";
$Document = "/var/www/html/txpay/ag/";
define("WEB_PATH", $Document);
define("TPL_PATH", $Document."/tpl/");

$LANG = "zh-TW";

//加入共用檔
include(WEB_PATH ."lib/MSDB.php");
include(WEB_PATH ."lib/pub_library.php");
require_once(WEB_PATH ."lib/tpl_power.php");

//錯誤碼字典檔
include(WEB_PATH ."lang/errMsg.$LANG.php");

//DB連線
$db 	= new JCDB(DB_HOST,DB_NAME,DB_USER,DB_PWD);
$dbSL 	= new JCDB(DB_HOST_SL,DB_NAME_SL,DB_USER_SL,DB_PWD_SL);

$Session_mid = $_SESSION["Session_mid"];	//Session of Store.id
if (!$NOLOGIN && $Session_mid=="") die(EorJS("請登入!","index.php"));

register_shutdown_function("Close_All_db");
?>