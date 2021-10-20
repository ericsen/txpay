<?php
//對接資料
include("conf/config.php");

$tpl = new TemplatePower(TPL_PATH ."/apiconfig.html");
$tpl->assignInclude( "MENU", TPL_PATH."/menu.html" );
$tpl->prepare();

$act = GetVal("act","");

if ($act=="chgpwd") {
	//更換交易密碼
	$oldpass = GetVal("oldpass");
	$newpass = GetVal("newpass");
	$newpass2 = GetVal("newpass2");
	
	//valid
	if (strlen($newpass)<6) die(EorJS("新密碼必須6位英數字以上!!"));
	if ($newpass!=$newpass2)  die(EorJS("新密碼兩次必須輸入一樣!!"));
	if ($oldpass=="")  die(EorJS("舊密碼不可以是空白!!"));
	
	$SQL = "SELECT * FROM Store WITH(NOLOCK) WHERE mid=$Session_mid AND MTPID='$oldpass'";
	$db->query($SQL);
	if ($db->Rows==1) {
		$SQL = "UPDATE Store WITH(ROWLOCK) SET MTPID='$newpass' WHERE mid=$Session_mid";
		$db->query($SQL);

		//log this
		$SQL = "INSERT INTO logStore WITH(ROWLOCK) (sid,ltitle,ldesc,lnote) VALUES ($Session_mid,N'MTPID',N'變更API交易密碼','');";
		$db->query($SQL);

		die(EorJS("密碼變更完成!!"));
	} else {
		die(EorJS("舊密碼不正確!!"));	
	}
}

$SQL = "SELECT MKey FROM Store WITH(NOLOCK) WHERE mid=$Session_mid";
$db->query($SQL);
if ($db->Rows==0) die("ERROR : apiconfig");
$MKey = $db->val("MKey");

$tpl->assign("MKey",$MKey);

$tpl->printToScreen();
?>

