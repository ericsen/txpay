<?php
//多筆儲值
include("conf/config.php");

$tpl = new TemplatePower(TPL_PATH ."/webbatchdeposit.html");
$tpl->assignInclude( "MENU", TPL_PATH."/menu.html" );
$tpl->prepare();

$act = GetVal("act","");

$SQL = "SELECT MKey,MTPID,Balance FROM Store WITH(NOLOCK) WHERE mid=$Session_mid";
$db->query($SQL);
$MKey = $db->val("MKey");
$MTPID = $db->val("MTPID");
$Balance = money($db->val("Balance"));

$tpl->assignGlobal("BALANCE",$Balance);
$tpl->assignGlobal("TODAY",Date('Ymd'));

if ($act=="upload") {
		$fileContent = file_get_contents($_FILES['CSVFILE']['tmp_name']);
		$tpl->newBlock("REDIR");
		$NO=0;
		$TAMOUNT = 0;

		foreach(preg_split("/((\r?\n)|(\r\n?))/", $fileContent) as $line){
			//0繳款方式(必填),1金額(必填),2商品名稱,3內部訂單編號,4姓名,5email,6手機,7備註1,8備註2,9有效期限,10關連Username
			$ary = explode(',', $line);
			if (($ary[0]=="CSPM" || $ary[0]=="VBANK") && is_numeric($ary[1])) {
					$tpl->newBlock("DATA");
					$NO++;
					$TAMOUNT = $TAMOUNT + $ary[1];
					if ($ary[3]=="") {	//內部訂單號為空
						$ary[3] = "UB" . Date('Ymd') . "RR" . mt_rand( 10000000, 99999999);
					}
					if ($ary[9]=="") {	//有效日期
						$ary[9] = date("Ymd", strtotime("+2 day"));
					}
					$tpl->assign(Array(
						"NO"	=> $NO,
						"PayMethod"	=>	$ary[0],
						"Amount"	=>	money($ary[1]),
						"PayInfo"	=>	$ary[2],
						"StoreOrderId"	=>	$ary[3],
						"PayName"	=>	$ary[4],
						"PayEmail"	=>	$ary[5],
						"PayPhone"	=>	$ary[6],
						"Note1"	=>	$ary[7],
						"Note2"	=>	$ary[8],
						"ExpiredDate"	=>	$ary[9],
						"UserId"	=>	$ary[10],
					));
					$line = join(",",$ary);
					$jary[$NO] = $line;
			}
		} 	
		$tpl->gotoBlock("REDIR");
		$tpl->assign("CONTENT",json_encode($jary));

		$tpl->assign("TCOUNT",$NO);
		$tpl->assign("TAMOUNT",money($TAMOUNT));
} if ($act=="deposit") {
		$fileContent = GetVal("content");
		$jary = json_decode($fileContent,true);
		foreach ($jary as $ita) {
			//0繳款方式(必填),1金額(必填),2商品名稱,3內部訂單編號,4姓名,5email,6手機,7備註1,8備註2,9有效期限,10關連Username
			$pmt = explode(",",$ita);
			
			$PayMethod = $pmt[0];
			$Amount = $pmt[1];
			$PayInfo = $pmt[2];
			$StoreOrderId = $pmt[3];
			$Currency = "TWD";
			$PayName = $pmt[4];
			$PayEmail = $pmt[5];
			$PayPhone = $pmt[6];
			$Note1 = $pmt[7];
			$Note2 = $pmt[8];
			$ExpireDate = $pmt[9];
			$UserId = $pmt[10];
			$ReturnURL = "http://www.null.com";
			
			if ($Amount=="") die("4301");
			if ($Amount=="0") die("4302");
			if (!preg_match("/^[1-9][0-9]*$/",$Amount)) die("4303");
			if ($Currency!="TWD") die("4310");
			$pt = array("CSPM", "VBANK");
			if (!in_array($PayMethod,$pt,true)) die("4320");
			if (!ctype_digit($PayPhone)) die("4341");
			if ($ExpireDate=="") die("4350");
			if (!preg_match("/^[0-9]{4}[0-9]{2}[0-9]{2}$/", $ExpireDate)) die("4351");
			//日期不可設為過去
			$edt = strtotime($ExpireDate . " 23:59:59");
			if ($edt < strtotime('now')) die("4352");
			
			//檢查系統是否營運中
			$SQL = "SELECT Payment FROM SysData WITH(NOLOCK) WHERE id=1";
			$db->query($SQL);
			if ($db->val("Payment")!=1) die("4102");
			
			//檢查代理代號是否正確
			$SQL = "SELECT mid,Enable,MTPID FROM Store WITH(NOLOCK) WHERE mid='$Session_mid'";
			$db->query($SQL);
			if($db->Rows)	{	//有資料
				//檢查代理帳號是否開放?
				if ($db->val("Enable")=="N") die("4203");
			} else {				//無資料
				die("4202");
			}
			$MTPID = $db->val("MTPID");
			$mid = $Session_mid;
			
			//更新Store.lastAPIAct
			$SQL = "UPDATE Store WITH(ROWLOCK) SET lastAPIAct=GetDate() WHERE mid=$mid";
			$db->query($SQL);
			
			//log this
			$RR = ms_escape_string($ita);
			$SQL = "INSERT INTO logStore WITH(ROWLOCK) (sid,ltitle,ldesc,lnote) VALUES ($mid,N'gateway',N'批次儲值$PayMethod',N'$RR');";
			$db->query($SQL);
			
			//收到支付請求

			$SQL = "SELECT [PaymentFee],[PaymentPercentage] FROM Store WITH(NOLOCK) WHERE mid=$mid";
			$db->query($SQL);
			$PaymentFee = $db->val("PaymentFee");//交易手續費
			$PaymentPercentage = $db->val("PaymentPercentage");
			$PlatformFee = round($Amount*$PaymentPercentage);	//平台手續費
			
			//紀錄
			$SQL = "INSERT INTO PaymentRequest WITH(ROWLOCK) 
			(mid,Amount,pgResult,TransFee,PlatformFee,Currency,PayMethod,PayInfo,StoreOrderId,PayName,PayPhone,PayEmail,Note1,Note2,ExpireDate,UserId,Chksum,ReturnURL) 
			VALUES 
			($mid,$Amount,'Pending',$PaymentFee,$PlatformFee,'$Currency','$PayMethod',N'$PayInfo',N'$StoreOrderId',N'$PayName',N'$PayPhone',N'$PayEmail',N'$Note1',N'$Note2','$ExpireDate',N'$UserId','$Chksum',N'$ReturnURL')";
			$prid = $db->insertId($SQL);
			
			//選擇第三方支付商家，批次一定用藍新，因為藍新可以不用按同意，直接產生
			$SQL = "SELECT sid,PGW,agentTPID,esafePayCode,esafeWebATM FROM PaymentGWStore WHERE Enable='Y' and PGW='neweb'";
			$db->query($SQL);
			if ($db->Rows==0) die("4101");
			$sid = $db->val("sid");
			$PGW = $db->val("PGW");
			$TPID = $db->val("agentTPID");
			
			$esafecode = "";
			if ($PayMethod=="CSPM") {
				$paymenttype = "MMK";
				$esafecode = $db->val("esafePayCode");	
				$acturl = "paycode.php";
			} else if ($PayMethod=="VBANK") {
				$paymenttype = "ATM";
				$esafecode = $db->val("esafeWebATM");	
				$acturl = "payatm.php";
			}
			
			//更新此筆交易所使用的第三商支付商家
			$SQL = "UPDATE PaymentRequest WITH(ROWLOCK) SET pgsid=$sid WHERE prid=$prid";
			$db->query($SQL);
			
			//導到藍新
			$ChkValue = strtoupper(md5($esafecode.$TPID.$Amount.$prid));

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://aquarius.newebpay.com.tw/CashSystemFrontEnd/Payment");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded;charset=UTF-8'));
			curl_setopt($ch, CURLOPT_POST, 1);
			$param = array(	
											'merchantnumber' => $esafecode,
											'ordernumber' => $prid,
											'amount' => $Amount,
											'paymenttype' => $paymenttype,
											'bankid' => "007",
											'paytitle' => $PayInfo,
											'paymemo' => $Note1,
											'payname' => $PayName,
											'payphone' => $PayPhone,
											'duedate' => $ExpireDate,
											'returnvalue' => "1",
											'hash' => $ChkValue,
											'nexturl' => "",
			);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$server_output = curl_exec($ch);
			$curlinfo = print_r(curl_getinfo($ch),true) ."\nParameters:\n". print_r($param,true);
			
			if ($server_output === FALSE) {
				//呼叫藍新失敗
				$errtext = htmlspecialchars(curl_error($ch));
				$fp = fopen('/home/txpay/gw/log/callNeweb.log', 'a');
				$RR = $RR . ">>".date("Y-m-d h:i:s")."==============".PHP_EOL;
				$RR = $RR . $curlinfo.PHP_EOL;
				fwrite($fp,$RR);
				fclose($fp);
				$RFAIL[] = $PayMethod.",$amount,呼叫第三方支付失敗"; 
			} else {
					if (substr($server_output,0,2) == "rc") {
						//商店收到成功  rc=0&amount=3000&merchantnumber=462076&ordernumber=191&paycode=ZZ18032400000532&checksum=61d39e715617031a26b5011bdca5cff5
						//bankid=&virtualaccount=
						parse_str($server_output,$pary);
						$amount = $pary["amount"];
						$merchantnumber = $pary["merchantnumber"];
						$ordernumber = $pary["ordernumber"];
						$paycode = $pary["paycode"];	//CSPM
						$checksum = $pary["checksum"];
						$bankid = $pary["bankid"];		//VBANK
						$virtualaccount = $pary["virtualaccount"]; //VBANK
						
						//更新PaymentRequest
						if ($PayMethod=="CSPM") {
							$SQL = "UPDATE PaymentRequest WITH(ROWLOCK) SET buysafeno='$ordernumber',Amount=$amount,pgPaycode='$paycode',pgPaytype='4,5,6,7',pgResult='PendingPayment',IP='$IP' WHERE prid=$ordernumber";
							$RDONE[] = $ita.",".$paycode;
						} else if ($PayMethod=="VBANK") {
							$SQL = "UPDATE PaymentRequest WITH(ROWLOCK) SET buysafeno='$ordernumber',Amount=$amount,EntityATM='$virtualaccount',BankCode='$bankid',pgResult='PendingPayment',IP='$IP' WHERE prid=$ordernumber";
							$RDONE[] = $ita.",".$bankid."-".$virtualaccount;

						}
						$db->query($SQL);
						
					} else {
						$fp = fopen('/home/txpay/gw/log/callNeweb.log', 'a');
						$RR = $RR . ">>".date("Y-m-d h:i:s")."==============".PHP_EOL;
						$RR = $RR . $curlinfo.PHP_EOL;
						$RR = $RR . "===========RESPONSE===========".PHP_EOL;
						$RR = $RR . $server_output.PHP_EOL;
						fwrite($fp,$RR);
						fclose($fp);
						$RFAIL[] = $PayMethod.",$amount,呼叫第三方支付失敗"; 
					}
			}
			curl_close ($ch);
		}	
			
			
		if (count($RDONE)>0) {
			$tpl->gotoBlock("_ROOT");
			$tpl->newBlock("RDONE");
			$TAMOUNT = 0;

			foreach ($RDONE as $ita) {
				//0繳款方式(必填),1金額(必填),2商品名稱,3內部訂單編號,4姓名,5email,6手機,7備註1,8備註2,9有效期限,10關連Username
				$pmt = explode(",",$ita);
				
				$PayMethod = $pmt[0];
				$Amount = $pmt[1];
				$PayInfo = $pmt[2];
				$StoreOrderId = $pmt[3];
				$Currency = "TWD";
				$PayName = $pmt[4];
				$PayEmail = $pmt[5];
				$PayPhone = $pmt[6];
				$Note1 = $pmt[7];
				$Note2 = $pmt[8];
				$ExpireDate = $pmt[9];
				$UserId = $pmt[10];
				$ReturnURL = "http://www.null.com";
			}

			$NO=0;
			foreach ($RDONE as $ita) {
				$tpl->newBlock("DATA1");
				$pmt = explode(",",$ita);//0繳款方式(必填),1金額(必填),2商品名稱,3內部訂單編號,4姓名,5email,6手機,7備註1,8備註2,9有效期限,10關連Username
				$NO++;
				$tpl->assign(Array(
					"NO"	=> $NO,
					"PayMethod"	=>	$pmt[0],
					"Amount"	=>	money($pmt[1]),
					"PayInfo"	=>	$pmt[2],
					"StoreOrderId"	=>	$pmt[3],
					"PayName"	=>	$pmt[4],
					"PayEmail"	=>	$pmt[5],
					"PayPhone"	=>	$pmt[6],
					"Note1"	=>	$pmt[7],
					"Note2"	=>	$pmt[8],
					"ExpiredDate"	=>	$pmt[9],
					"UserId"	=>	$pmt[10],
					"CODE" => $pmt[11],
				));
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


