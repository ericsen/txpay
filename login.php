<?php
$NotLogin = "Y";
include("../conf/config.php");

$Username	= GetVal("username", "");
$Pwd		= GetVal("pwd", "");

//登入
if($Username != ""){
	//檢查可登入IP
	$chkip = 0;
	$db->query("SELECT Ip FROM IpLoginSet WITH (NOLOCK) WHERE ComID = ". COMID);
	if($db->numRows()){
		do{
			$ip = $db->val("Ip");
			$pos = strpos($ip, "*");
			if($pos !== false) $ip = substr($ip, 0, $pos);
			if($ip == "" || strstr(getIP(),$ip)) {$chkip=1; break;}
		}while($db->nextRow());
	}
	if($chkip == 0) die(EorJs("IP not allowed : ". getIP(), WEB_HOST));

	//帳號檢查
	$db->query("SELECT Aid, Username, Pwd, Nick, IsSub, Status, Authority FROM Admin WITH (NOLOCK) WHERE ComID = ". COMID ." AND Username = '". $Username ."' AND Status <> 'D';");
	$Data = $db->val();

	if($db->Rows == 0)		die(EorJs($LVar["UserPwdEor"], WEB_HOST));	//檢查會員帳號
	if($Data["Pwd"] != $Pwd)	die(EorJs($LVar["UserPwdEor"], WEB_HOST));	//檢查密碼
	if($Data["Status"] == "N")	die(EorJs($LVar["UserStop"], WEB_HOST));	//檢查狀態是否停用

	//後踢前
	$db->query("DELETE FROM LoginAdmin WHERE Aid = ". $Data["Aid"]);

	$_User["Aid"]		= $Data["Aid"];
	$_User["Pwd"]		= $Data["Pwd"];
	$_User["Username"]	= $Data["Username"];
	$_User["IsSub"]		= $Data["IsSub"];
	$_User["FunStyle"] 	= ($Data["IsSub"] == 1) ? "none" : "block";

	$db->query("SELECT TOP 1 Cid FROM Customer WITH (NOLOCK) WHERE ComID = ". COMID ." AND IsAg = 1 AND Upid < 0 ORDER BY Cid ASC;");
	$_User["Cid"]		= $db->val("Cid");

	$UserStr = json_encode($_User);

	//寫入登入
	$db->query("INSERT INTO LoginAdmin WITH (ROWLOCK) (CGUID, ComID, Aid, Username, LoginTime, UserData, Langx, LS, Ip, Php_Self) VALUES (NEWID(), ". COMID .", ". $Data['Aid'] .", '". $Data['Username'] ."', '". TIME ."', '". $UserStr ."', '". $_COOKIE["Langx"] ."', '". $_COOKIE["LS"] ."', '". getIP() ."', '". $_SERVER["PHP_SELF"] ."');");
	//取出 CGUID
	$db->query("SELECT CAST(CGUID AS varchar(36)) AS CGUID FROM LoginAdmin WITH (ROWLOCK) WHERE Aid = ". $_User["Aid"] ." AND LoginTime = ". TIME .";");
	if($db->Rows){
		$_SESSION["CGUID"] = $db->val("CGUID");

		//寫入操作紀錄
		LogAdmin("@login", "@success");
		Header("Location:body.php");
		exit;
	}
}

if($_SESSION["CGUID"] != ""){
	$db->query("SELECT Aid FROM LoginAdmin WITH (ROWLOCK) WHERE CGUID = '". $_SESSION["CGUID"] ."';");
	if($db->Rows){
		Header("Location:body.php");
		exit;
	}
}

$tpl = new TemplatePower(TPL_PATH ."login.html");
$tpl->includeLang();
$tpl->assign(array(
	"LANGX"	=> $_COOKIE["Langx"],
	"IP"	=> getIP(),
));
$tpl->printToScreen();
?>