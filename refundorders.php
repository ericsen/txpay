<?php
//退款紀錄
include("conf/config.php");

$XLS = GetVal("download","");
if ($XLS=="excel") {
	$tpl = new TemplatePower(TPL_PATH ."/refundorders-xls.html");
	header('Content-type:application/vnd.ms-excel');  //宣告網頁格式
  header('Content-Disposition: attachment; filename=lists.xls');  //設定檔案名稱	
} else {
	$tpl = new TemplatePower(TPL_PATH ."/refundorders.html");
	$tpl->assignInclude( "MENU", TPL_PATH."/menu.html" );
}
$tpl->prepare();

$act = GetVal("act","");
if ($act=="list") {
	
	$PRC["Initialize"] = "處理中";
	$PRC["Pending"] = "處理中";
	$PRC["Paid"] = "已退款";
	$PRC["Cancel"] = "失敗/取消";
	$PRC["Fail"] = "失敗/取消";
	
	$SDATE = GetVal("sdate",date('Y-m-d'));
	$EDATE = GetVal("edate",date('Y-m-d'));
	
	$RESULT = GetVal("result","S1");	//付款狀態 S1:all S2:處理中 S3:已退款 S4:失敗取消
	$KEYWORD = GetVal("keyword","");	//關鍵字
	$Page = GetVal("page", 1);
	$NOPAGE = GetVal("nopage","");	//不分頁Y
	
	$sSQL .= "rdatetime BETWEEN '$SDATE 00:00:00' AND '$EDATE 23:59:59' AND ";

	switch ($RESULT) {	//付款狀態
		case "S1" :	//全部
			break;
		case "S2" :	//處理中
			$sSQL .= "pgResult IN ('Initialize','Pending') AND ";
			break;
		case "S3" :	//已退款
			$sSQL .= "pgResult='Paid' AND ";
			break;
		case "S4" :	//失敗取消
			$sSQL .= "pgResult IN ('Cancel','Fail') AND ";
			break;
	}
	
	if ($KEYWORD!="") {	//關鍵字
		$sSQL .= "(rid like '%$KEYWORD%' OR StoreOrderId like '%$KEYWORD%' OR VAccountName like N'%$KEYWORD%' OR VBankAccount like '%$KEYWORD%' OR 
		Note1 like N'%$KEYWORD%' OR Note2 like N'%$KEYWORD%' OR UserId like '%$KEYWORD%') AND ";
	}

	$SQL = "SELECT [rid],[Amount],[StoreOrderId],[VBankAccount],[VBankCode],[VBankName],[VAccountName],[Note1],[Note2],[UserId],[rdatetime],[pgResult],
	[errcode],[errmsg],[PayDate],[PayTime],[TransFee],[PlatformFee],[TotalAmount]  FROM ReturnRequest WHERE mid=$Session_mid AND $sSQL 1=1 ORDER BY rid desc";
	
	$db->MaxRows = 30;
	$db->PageNo  = $Page;

	if ($NOPAGE=="true") {
		$db->query($SQL);
	} else {	//分頁
		$tpl->newBlock("SELPAGE");	//顯示換頁
		$db->queryPage($SQL);
	}

	$tpl->assignGlobal(Array(
	  "PAGE_OPTION" => PageOption($db->PageNo, $db->TotalPage),
	  "PAGE_TOTAL"  => $db->TotalPage,
	));

	$TOTALCOUNT=$db->Rows;
	$PAIDCOUNT=0;
	$NOPAYCOUNT=0;
	$PAIDTOTAL=0;
	$TFTOTAL=0;
	$PTTOTAL=0;
	$TOTAL=0;

	$ID=$db->MaxRows*($Page-1);
	if ($db->Rows>0) {
		$Data = $db->all_Data();
		foreach ($Data as $v) {
			$ID++;
			$tpl->newBlock("DATA");
    	$tpl->assign(Array(
    				"ID" => $ID,
    				"RDATETIME" => $v["rdatetime"],
    				"RID" => $v["rid"],
    				"STOREORDERID" => $v["StoreOrderId"],
    				"VACCOUNTNAME" => $v["VAccountName"],
    				"VCODE" => $v["VBankCode"]."-".$v["VBankAccount"],
    				"PGRESULT" => $PRC[$v["pgResult"]],
    				"AMOUNT" => money($v["Amount"]),
    				"TRANSFEE" => money($v["TransFee"],2),
    				"PLATFORMFEE" => money($v["PlatformFee"],2),
    				"TOTALAMOUNT" => money($v["TotalAmount"],2),
    				"ERRMSG" => $v["errmsg"],
    				"PAIDDATE" => $v["PayDate"]. " " . $v["PayTime"],
    				"NOTE1" => $v["Note1"],
    				"NOTE2" => $v["Note2"],
    				"USERID" => $v["UserId"],
			));
			$PAIDTOTAL += $v["Amount"];
			$TFTOTAL += $v["TransFee"];
			$PTTOTAL += $v["PlatformFee"];
			if ($v["pgResult"]=="Paid") $PAIDCOUNT++;
		}
		$tpl->newBlock("SUMMARY");
	} else {
		$tpl->newBlock("NODATA");
	}

//本頁小計總交易筆數：{TOTALCOUNT} 筆 已付款筆數：{PAIDCOUNT} 筆 未付款筆數：{NOPAYCOUNT} 筆 已付款金額：{PAIDTOTAL} 元 交易手續費：{TFTOTAL} 元 平台手續費：{PTTOTAL} 元 實收金額：{TOTAL} 元	
	$tpl->assignGlobal("TOTALCOUNT",$TOTALCOUNT);
	$tpl->assignGlobal("PAIDCOUNT",$PAIDCOUNT);
	$tpl->assignGlobal("NOPAYCOUNT",$TOTALCOUNT-$PAIDCOUNT);
	$tpl->assignGlobal("PAIDTOTAL",money($PAIDTOTAL));
	$tpl->assignGlobal("TFTOTAL",money($TFTOTAL,2));
	$tpl->assignGlobal("PTTOTAL",money($PTTOTAL,2));
	$tpl->assignGlobal("TOTAL",$TOTAL);




	$tpl->assignGlobal("SDATE",$SDATE);
	$tpl->assignGlobal("EDATE",$EDATE);
	$tpl->assignGlobal("OTYPE",$OTYPE);
	$tpl->assignGlobal("RESULT",$RESULT);
	$tpl->assignGlobal("PAYFROM",$PAYFROM);
	$tpl->assignGlobal("KEYWORD",$KEYWORD);
	$tpl->assignGlobal("NOPAGE",$NOPAGE);
	
	$tpl->assignGlobal("RSHOW","");	
	
	
} else {
	$tpl->assign("SDATE",date('Y-m-d'));
	$tpl->assign("EDATE",date('Y-m-d'));
	$tpl->assign("RSHOW","none");	
}
$tpl->printToScreen();
?>


