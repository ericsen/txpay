<?php
include ("../conf/config.php");

$tpl = new TemplatePower(TPL_PATH ."main.html");
$tpl->includeLang();

$tpl->printToScreen();
?>