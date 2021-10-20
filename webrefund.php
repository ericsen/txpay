<?php
//退款
include("conf/config.php");

$tpl = new TemplatePower(TPL_PATH ."/webrefund.html");
$tpl->assignInclude( "MENU", TPL_PATH."/menu.html" );
$tpl->prepare();

$act = GetVal("act","");

$SQL = "SELECT MKey,MTPID,Balance FROM Store WITH(NOLOCK) WHERE mid=$Session_mid";
$db->query($SQL);
$MKey = $db->val("MKey");
$MTPID = $db->val("MTPID");
$Balance = money($db->val("Balance"));

$tpl->assignGlobal("BALANCE",$Balance);


if ($act=="refund") {
	//確定產生支付碼
	
	$amount = GetVal("amount","0");
	$storeorderid = GetVal("storeorderid","");
	$VBankCode = GetVal("VBankCode","");
	$VBankName = GetVal("VBankName","");
	$VBankAccount = GetVal("VBankAccount","");
	$VAccountName = GetVal("VAccountName","");
	$note1 = GetVal("note1","");
	$note2 = GetVal("note2","");
	$userid = GetVal("userid","");
	
	$MD5Code = md5($MKey.$MTPID.$amount.$storeorderid);
		
	$tpl->newBlock("REDIR");
	$tpl->assign(Array(
		"StoreID"	=>	$MKey,
		"Amount"	=>	$amount,
		"StoreOrderId"	=>	$storeorderid,
		"VBankAccount"	=>	$VBankAccount,
		"VBankCode"	=>	$VBankCode,
		"VBankName"	=>	$VBankName,
		"VAccountName"	=>	$VAccountName,
		"Note1"	=>	$note1,
		"Note2"	=>	$note2,
		"UserId"	=>	$userid,
		"ReturnURL"	=>	"http://null.com",
		"Chksum"	=>	$MD5Code,
	));
}

$tpl->assign("EXPIREDATE",date('Ymd',strtotime('tomorrow')));
$tpl->printToScreen();
?>


