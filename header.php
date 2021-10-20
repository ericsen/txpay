<?php
include ("../conf/config.php");

$tpl = new TemplatePower(TPL_PATH ."header.html");
$tpl->includeLang();

$tpl->printToScreen();
?>