<?php
//多筆退款
include("conf/config.php");

$tpl = new TemplatePower(TPL_PATH ."/webbatchrefund.html");
$tpl->assignInclude( "MENU", TPL_PATH."/menu.html" );
$tpl->prepare();

$act = GetVal("act","");

$SQL = "SELECT MKey,MTPID,Balance FROM Store WITH(NOLOCK) WHERE mid=$Session_mid";
$db->query($SQL);
$MKey = $db->val("MKey");
$MTPID = $db->val("MTPID");
$Balance = money($db->val("Balance"));

$tpl->assignGlobal("BALANCE",$Balance);

if ($act=="upload") {
		$fileContent = file_get_contents($_FILES['CSVFILE']['tmp_name']);
		$tpl->newBlock("REDIR");
		$NO=0;
		$TAMOUNT = 0;
		
		foreach(preg_split("/((\r?\n)|(\r\n?))/", $fileContent) as $line){
			//銀行代號,帳號,金額,帳戶名,銀行名稱,內部訂單編號(唯一),備註1,備註2,關聯Username,Chksum
			$ary = explode(',', $line);
			if (is_numeric($ary[0]) && strlen($ary[1])>5 && strlen($ary[2])>=4 && strlen($ary[5])>1) {	//銀行帳號是數字
					$tpl->newBlock("DATA");
					$NO++;
					$TAMOUNT = $TAMOUNT + $ary[2];
					$MD5Code = md5($MKey.$MTPID.$ary[2].$ary[5]);
					$tpl->assign(Array(
						"NO"	=> $NO,
						"VBankCode"	=>	$ary[0],
						"VBankAccount"	=>	$ary[1],
						"Amount"	=>	money($ary[2]),
						"VAccountName"	=>	$ary[3],
						"VBankName"	=>	$ary[4],
						"StoreOrderId"	=>	$ary[5],
						"Note1"	=>	$ary[6],
						"Note2"	=>	$ary[7],
						"UserId"	=>	$ary[8],
						"Chksum" => $MD5Code,
					));
					$jary[$NO] = $line.",".$MD5Code;
			}
		} 	
		$tpl->gotoBlock("REDIR");
		$tpl->assign("CONTENT",json_encode($jary));

		$tpl->assign("TCOUNT",$NO);
		$tpl->assign("TAMOUNT",money($TAMOUNT));
} if ($act=="refund") {
		$fileContent = GetVal("content");
		$jary = json_decode($fileContent,true);
		foreach ($jary as $ita) {
			//0銀行代號,1帳號,2金額,3帳戶名,4銀行名稱,5內部訂單編號(唯一),6備註1,7備註2,8關聯Username,9Chksum
			$pmt = explode(",",$ita);
			//CURL 一筆一筆處理退款 	
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"$GWURL/api3/refund.php");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded;charset=UTF-8'));
			curl_setopt($ch, CURLOPT_POST, 1);
			$param = array(	
					"StoreID" => $MKey,
					"VBankCode" => $pmt[0],
					"VBankAccount" => $pmt[1],
					"Amount" => $pmt[2],
					"VAccountName" => $pmt[3],
					"VBankName" => $pmt[4],
					"StoreOrderId" => $pmt[5],
					"Note1" => $pmt[6],
					"Note2" => $pmt[7],
					"UserId" => $pmt[8],
					"Chksum" => $pmt[9],
					"PayMethod" => "RETURN",
					"Currency" => "TWD",
					"ReturnURL" => "http://null.com",
			);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$server_output = curl_exec($ch);
			if ($server_output === FALSE) {
				//CURL 失敗
				$RFAIL[] = $pmt[0]."-".$pmt[1].",$pmt[2],$server_output"; 
			} else {
				$rary = json_decode($server_output,true);
				if ($rary["Result"]=="OK") {
					$RDONE[] = $pmt[0]."-".$pmt[1].",$pmt[2]"; 
				} else {
					$RFAIL[] = $pmt[0]."-".$pmt[1].",$pmt[2],$server_output"; 
				}
			}
			curl_close ($ch);
		}
		
		if (count($RDONE)>0) {
			$tpl->gotoBlock("_ROOT");
			$tpl->newBlock("RDONE");
			$TAMOUNT = 0;
			foreach ($RDONE as $ita) {
				$tpl->newBlock("DATA1");
				$pmt = explode(",",$ita);
				$tpl->assign("VBankAccount",$pmt[0]);
				$tpl->assign("Amount",$pmt[1]);
				$TAMOUNT = $TAMOUNT + $pmt[1];
			}
			$tpl->gotoBlock("RDONE");
			$tpl->assign("TAMOUNT",$TAMOUNT);
		}
		
		if (count($RFAIL)>0) {
			$tpl->gotoBlock("_ROOT");
			$tpl->newBlock("RFAIL");
			$TAMOUNT = 0;
			foreach ($RFAIL as $ita) {
				$tpl->newBlock("DATA2");
				$pmt = explode(",",$ita);
				$tpl->assign("VBankAccount",$pmt[0]);
				$tpl->assign("Amount",$pmt[1]);
				$tpl->assign("ERR",$pmt[2].":".$ERRMSG[$pmt[2]]);
			}
		}
}

$tpl->printToScreen();
?>


