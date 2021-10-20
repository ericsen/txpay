<?php
include("../conf/config.php");

$tpl = new TemplatePower(TPL_PATH ."body.html");
$tpl->includeLang();

$tpl->printToScreen();
?>