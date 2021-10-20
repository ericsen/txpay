<?php
//商店基本資料
include("conf/config.php");

$tpl = new TemplatePower(TPL_PATH ."/info.html");
$tpl->assignInclude( "MENU", TPL_PATH."/menu.html" );
$tpl->prepare();

$SQL = "SELECT MName,ContactName,ContactPhone,ContactMobile,ContactEmail,rdatetime FROM Store WITH(NOLOCK) WHERE mid=$Session_mid";
$db->query($SQL);
if ($db->Rows==0) die("ERROR : main11");
$MName = $db->val("MName");
$ContactName = $db->val("ContactName");
$ContactPhone = $db->val("ContactPhone");
$ContactMobile = $db->val("ContactMobile");
$ContactEmail = $db->val("ContactEmail");
$rdatetime = $db->val("rdatetime");
$rdatetime = date('Y-m-d',strtotime($rdatetime));

$tpl->assign("STORENAME",$MName);
$tpl->assign("CONTACTNAME",$ContactName);
$tpl->assign("CONTACTPHONE",$ContactPhone);
$tpl->assign("CONTACTMOBILE",$ContactMobile);
$tpl->assign("CONTACTEMAIL",$ContactEmail);
$tpl->assign("RDATETIME",$rdatetime);

$tpl->printToScreen();
?>

