<?php
//補水與提領記錄
include("conf/config.php");

$tpl = new TemplatePower(TPL_PATH ."/storebalancelist.html");
$tpl->assignInclude( "MENU", TPL_PATH."/menu.html" );
$tpl->prepare();

$act = GetVal("act","");
if ($act=="list") {

	$SDATE = GetVal("sdate",date('Y-m-d'));
	$EDATE = GetVal("edate",date('Y-m-d'));
	
	$LTYPE = GetVal("ltype","");
	$KEYWORD = GetVal("keyword","");	//關鍵字
	$Page = GetVal("page", 1);
	
	$LPM["addfund"] = "充值餘額";
	$LPM["withdrawal"] = "<span style='color:red'>提領餘額</span>";
	
	$sSQL .= "ldatetime BETWEEN '$SDATE 00:00:00' AND '$EDATE 23:59:59' AND ";
	
	switch ($LTYPE) {	//狀態
		case "" :	//全部
			break;
		case "addfund" :	//補水
			$sSQL .= "ltype='addfund' AND ";
			break;
		case "withdrawal" :	//提領
			$sSQL .= "ltype='withdrawal' AND ";
			break;
	}
	
	if ($KEYWORD!="") {	//關鍵字
		$sSQL .= "(lnote like '%$KEYWORD%') AND ";
	}

	$SQL = "SELECT lid,[ldatetime],ltype,Amount,lnote FROM [logStorePayINOUT] where mid=$Session_mid AND $sSQL 1=1 ORDER BY lid desc";
	//die(	$SQL	);
//	$db->query($SQL);


	$db->MaxRows = 30;
	$db->PageNo  = $Page;
	$db->queryPage($SQL);
	$tpl->assignGlobal(Array(
	  "PAGE_OPTION" => PageOption($db->PageNo, $db->TotalPage),
	  "PAGE_TOTAL"  => $db->TotalPage,
	));

	$TOTALCOUNT=$db->Rows;

	$ID=$db->MaxRows*($Page-1);
	if ($db->Rows>0) {
		$Data = $db->all_Data();
		foreach ($Data as $v) {
			$ID++;
			$tpl->newBlock("DATA");
    	$tpl->assign(Array(
    				"ID" => $ID,
    				"RDATETIME" => $v["ldatetime"],
    				"LID" => $v["lid"],
    				"LTYPE" => $LPM[$v["ltype"]],
    				"AMOUNT" => money($v["Amount"]),
    				"NOTE" => $v["lnote"],
			));
			
			
		}
	} else {
		$tpl->newBlock("NODATA");
	}

	$tpl->assignGlobal("SDATE",$SDATE);
	$tpl->assignGlobal("EDATE",$EDATE);
	$tpl->assignGlobal("LTYPE",$LTYPE);
	$tpl->assignGlobal("KEYWORD",$KEYWORD);
	
	$tpl->assignGlobal("RSHOW","");	
	
	
} else {
	$tpl->assign("SDATE",date('Y-m-d'));
	$tpl->assign("EDATE",date('Y-m-d'));
	$tpl->assign("RSHOW","none");	
}
$tpl->printToScreen();
?>


