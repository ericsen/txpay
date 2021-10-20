<?php
//訂單紀錄
include("conf/config.php");

$XLS = GetVal("download","");
if ($XLS=="excel") {
	$tpl = new TemplatePower(TPL_PATH ."/payorders-xls.html");
	header('Content-type:application/vnd.ms-excel');  //宣告網頁格式
  header('Content-Disposition: attachment; filename=lists.xls');  //設定檔案名稱	
} else {
	$tpl = new TemplatePower(TPL_PATH ."/payorders.html");
	$tpl->assignInclude( "MENU", TPL_PATH."/menu.html" );
}
$tpl->prepare();

$act = GetVal("act","");
if ($act=="list") {
	
	$PMC["CSPM"] = "超商代收";
	$PMC["VBANK"] = "ATM虛擬帳號";
	
	$PRC["Initialize"] = "未繳款";
	$PRC["Pending"] = "未繳款";
	$PRC["PendingPayment"] = "未繳款";
	$PRC["Paid"] = "已付款";
	$PRC["Transfered"] = "已付款";
	$PRC["Expired"] = "訂單失敗:超過支付期限";
	$PRC["Cancel"] = "取消";
	$PRC["Fail"] = "取消";
	
	$SDATE = GetVal("sdate",date('Y-m-d'));
	$EDATE = GetVal("edate",date('Y-m-d'));
	
	$OTYPE = GetVal("otype","D1");	//查詢日期種類 D1:訂單日 D2:付款日 D3:撥款日
	$RESULT = GetVal("result","S1");	//付款狀態 S1:all S2:已付款 S3:未付款
	$PAYFROM = GetVal("payfrom","T1");	//付款來源 T1:all T2:超商 T3:虛擬帳號
	$KEYWORD = GetVal("keyword","");	//關鍵字
	$Page = GetVal("page", 1);
	$NOPAGE = GetVal("nopage","");	//不分頁Y
	
	switch ($OTYPE) {	//訂單日
		case "D1" :	//訂單日
			$sSQL .= "rdatetime BETWEEN '$SDATE 00:00:00' AND '$EDATE 23:59:59' AND ";
			break;
		case "D2" :	//付款日
			$sSQL .= "PayDate BETWEEN '$SDATE 00:00:00' AND '$EDATE 23:59:59' AND ";
			break;
		case "D3" :	//撥款日
			$sSQL .= "PaidStoreDatetime BETWEEN '$SDATE 00:00:00' AND '$EDATE 23:59:59' AND ";
			break;
	}
	
	switch ($RESULT) {	//付款狀態
		case "S1" :	//全部
			break;
		case "S2" :	//已付款
			$sSQL .= "pgResult IN ('Paid','Transfered') AND ";
			break;
		case "S3" :	//未付款
			$sSQL .= "pgResult IN ('Initialize','Pending','PendingPayment') AND ";
			break;
		case "S4" :	//取消
			$sSQL .= "pgResult IN ('Expired','Cancel','Fail') AND ";
			break;
		case "S5" :	//已撥款
			$sSQL .= "pgResult='Transfered' AND ";
			break;
		case "S6" :	//已付款未撥款
			$sSQL .= "pgResult='Paid' AND ";
			break;
	}
	
	switch ($PAYFROM) {	//付款狀態
		case "T1" :	//全部
			break;
		case "T2" :	//超商
			$sSQL .= "PayMethod='CSPM' AND ";
			break;
		case "T3" :	//虛擬帳號
			$sSQL .= "PayMethod='VBANK' AND ";
			break;
	}
	
	if ($KEYWORD!="") {	//關鍵字
		$sSQL .= "(prid like '%$KEYWORD%' OR StoreOrderId like '%$KEYWORD%' OR pgPayCode like '%$KEYWORD%' OR EntityATM like '%$KEYWORD%' OR 
		BankCode like '%$KEYWORD%' OR StoreOrderId like '%$KEYWORD%' OR PayInfo like N'%$KEYWORD%' OR Note1 like N'%$KEYWORD%' OR Note2 like N'%$KEYWORD%' OR
		 UserId like '%$KEYWORD%') AND ";
	}

	$SQL = "SELECT [rdatetime],prid,[StoreOrderId],[PayMethod],[pgPaycode],LEFT(BankCode,3) as BankCode,[EntityATM],[pgResult],[Amount],TransFee,PlatformFee,[PaidStoreAmount],[PaidStoreDatetime],[PayInfo],[PayName],[PayPhone],[PayEmail],[Note1],[Note2],[UserId],[Hold],[HoldDatetime],[PayDate],[PayTime] FROM [PaymentRequest] where mid=$Session_mid AND $sSQL 1=1 ORDER BY prid desc";
//	$db->query($SQL);


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
    				"PRID" => $v["prid"],
    				"STOREORDERID" => $v["StoreOrderId"],
    				"PAYMETHOD" => $PMC[$v["PayMethod"]],
    				"PGRESULT" => $PRC[$v["pgResult"]]."<br>".$v["PayDate"]." ".substr($v["PayTime"],0,2).":".substr($v["PayTime"],2,2),
    				"AMOUNT" => money($v["Amount"]),
    				"TRANSFEE" => money($v["TransFee"],2),
    				"PLATFORMFEE" => money($v["PlatformFee"],2),
    				"PAYINFO" => $v["PayInfo"],
    				"PAYNAME" => $v["PayName"],
    				"PAYPHONE" => $v["PayPhone"],
    				"PAYEMAIL" => $v["PayEmail"],
    				"NOTE1" => $v["Note1"],
    				"NOTE2" => $v["Note2"],
    				"USERID" => $v["UserId"],
			));
			
			if ($v["pgResult"]=="Paid" || $v["pgResult"]=="Transfered") $PAIDTOTAL += $v["Amount"];
			$TFTOTAL += $v["TransFee"];
			$PTTOTAL += $v["PlatformFee"];
			if ($v["PayMethod"]=="CSPM") $tpl->assign("VCODE",$v["pgPaycode"]);
			if ($v["PayMethod"]=="VBANK") $tpl->assign("VCODE",$v["BankCode"]."-".$v["EntityATM"]);
			if ($v["Hold"]==1) {
				//保留
				$tpl->assign("HOLD","<span style='color:red' title='保留日:".$v["HoldDatetime"]."'>保留款</span>");
			}
			if ($v["PaidStoreAmount"]>0) {
				$tpl->assign("PAIDSTOREAMOUNT",money($v["PaidStoreAmount"],2));
				$tpl->assign("PAIDSTOREDATETIME","撥款日：".date("Y-m-d",strtotime($v["PaidStoreDatetime"])));
			}
			
			if ($v["pgResult"]=="Paid" || $v["pgResult"]=="Transfered") $PAIDCOUNT++;
			
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


