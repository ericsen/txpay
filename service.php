<?php
//聯絡客服
include("conf/config.php");

$tpl = new TemplatePower(TPL_PATH ."/service.html");
$tpl->assignInclude( "MENU", TPL_PATH."/menu.html" );
$tpl->prepare();

$tpl->printToScreen();
?>

