<?php
//手動產生儲值碼
include("conf/config.php");

$tpl = new TemplatePower(TPL_PATH ."/webdeposit.html");
$tpl->assignInclude( "MENU", TPL_PATH."/menu.html" );
$tpl->prepare();

$act = GetVal("act","");

if ($act=="pay") {
	//確定產生支付碼
	
	$ptype = GetVal("ptype","CSPM");
	$amount = GetVal("amount","0");
	$payinfo = GetVal("payinfo","");
	$storeorderid = GetVal("storeorderid","");
	$payname = GetVal("payname","");
	$payphone = GetVal("payphone","");
	$payemail = GetVal("payemail","");
	$note1 = GetVal("note1","");
	$note2 = GetVal("note2","");
	$expiredate = GetVal("expiredate","");
	$userid = GetVal("userid","");
	$bankid = GetVal("bankid","");
	
	
	$SQL = "SELECT MKey,MTPID FROM Store WITH(NOLOCK) WHERE mid=$Session_mid";
	$db->query($SQL);
	$MKey = $db->val("MKey");
	$MTPID = $db->val("MTPID");
	
	$MD5Code = md5($MKey.$MTPID.$amount);
		
	$tpl->newBlock("REDIR");
	$tpl->assign(Array(
		"StoreID"	=>	$MKey,
		"Amount"	=>	$amount,
		"PayMethod"	=>	$ptype,
		"PayInfo"	=>	$payinfo,
		"StoreOrderId"	=>	$storeorderid,
		"PayName"	=>	$payname,
		"PayPhone"	=>	$payphone,
		"PayEmail"	=>	$payemail,
		"Note1"	=>	$note1,
		"Note2"	=>	$note2,
		"ExpireDate"	=>	$expiredate,
		"UserId"	=>	$userid,
		"ReturnURL"	=>	"http://null.com",
		"Chksum"	=>	$MD5Code,
		"Bankid"	=> $bankid,
	));
	
}
$tpl->assign("EXPIREDATE",date('Ymd',strtotime('tomorrow')));
$tpl->printToScreen();
?>


