<?php
require_once(WEB_PATH .'lib/ip2location.class.php');

function ms_escape_string($data) {
        if ( !isset($data) or empty($data) ) return '';
        if ( is_numeric($data) ) return $data;

        $non_displayables = array(
            '/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
            '/%1[0-9a-f]/',             // url encoded 16-31
            '/[\x00-\x08]/',            // 00-08
            '/\x0b/',                   // 11
            '/\x0c/',                   // 12
            '/[\x0e-\x1f]/'             // 14-31
        );
        foreach ( $non_displayables as $regex )
            $data = preg_replace( $regex, '', $data );
        $data = str_replace("'", "''", $data );
        return $data;
    }


//------關閉所有DB連線------
function Close_All_db(){
	global $db, $dbSL, $dbAV;
	if(!empty($db->conn))	$db->close();
	if(!empty($dbSL->conn))	$dbSL->close();
	if(!empty($dbAV->conn))	$dbAV->close();
}

//登入檢查 並取出 _User
function ChkLogin(){
	global $db, $LVar, $_User;

	$Str  = "alert('". $LVar["Kick"] ."');\n";
	$Str .= "top.location.replace('". WEB_HOST ."');\n";
	$UserStr = "";
	if($_SESSION["CGUID"] != ""){
		//更新自己ㄉ時間
		$db->query("UPDATE LoginAdmin WITH(ROWLOCK) SET LoginTime = ". TIME .", Php_Self = '". $_SERVER["PHP_SELF"] ."' WHERE ComID = ". COMID ." AND CGUID = '". $_SESSION["CGUID"] ."';");
		$db->query("SELECT UserData FROM LoginAdmin WITH(NOLOCK) WHERE ComID = ". COMID ." AND CGUID = '". $_SESSION["CGUID"] ."';");
		if($db->Rows){
			$UserStr	= $db->val("UserData");
			$_User		= json_decode($UserStr, true);
		}
	}
	if($UserStr == "")	die(ExecJS($Str));
}

//取得本月 今日 昨日 等相關日期
function GetDfDateAry($Date=""){
	if($Date == "")	$Today = DateSet(TODAY, "Y-m-d");
	else		$Today = DateSet($Date, "Y-m-d");
	$Ary = getdate(strtotime($Today));

	//星期天調整
	$Ary["wday_1"] = ($Ary["wday"] == 0) ? 7 : $Ary["wday"];

	//今天
	$Return["Today"]	= $Today;
	//前一日
	$Return["Yesterday"]	= date("Y-m-d", mktime(0, 0, 0, $Ary["mon"], ($Ary["mday"] - 1), $Ary["year"]));
	//後一日
	$Return["Tomorrow"]	= date("Y-m-d", mktime(0, 0, 0, $Ary["mon"], ($Ary["mday"] + 1), $Ary["year"]));
	//本週
	$Return["Week_S"]	= date("Y-m-d", mktime(0, 0, 0, $Ary["mon"], ($Ary["mday"] - $Ary["wday_1"] + 1), $Ary["year"]));
	$Return["Week_E"]	= date("Y-m-d", mktime(0, 0, 0, $Ary["mon"], ($Ary["mday"] - $Ary["wday_1"] + 7), $Ary["year"]));
	//上週
	$Return["Week_L_S"]	= date("Y-m-d", mktime(0, 0, 0, $Ary["mon"], ($Ary["mday"] - $Ary["wday_1"] + 1 - 7), $Ary["year"]));
	$Return["Week_L_E"]	= date("Y-m-d", mktime(0, 0, 0, $Ary["mon"], ($Ary["mday"] - $Ary["wday_1"] + 7 - 7), $Ary["year"]));
	//上上週
	$Return["Week_LL_S"]	= date("Y-m-d", mktime(0, 0, 0, $Ary["mon"], ($Ary["mday"] - $Ary["wday_1"] + 1 - 14), $Ary["year"]));
	$Return["Week_LL_E"]	= date("Y-m-d", mktime(0, 0, 0, $Ary["mon"], ($Ary["mday"] - $Ary["wday_1"] + 7 - 14), $Ary["year"]));
	//前一週
	$Return["Week_B_S"]	= date("Y-m-d", mktime(0, 0, 0, $Ary["mon"], ($Ary["mday"] - $Ary["wday"] + 1 - 7), $Ary["year"]));
	$Return["Week_B_E"]	= date("Y-m-d", mktime(0, 0, 0, $Ary["mon"], ($Ary["mday"] - $Ary["wday"] + 7 - 7), $Ary["year"]));
	//後一週
	$Return["Week_N_S"]	= date("Y-m-d", mktime(0, 0, 0, $Ary["mon"], ($Ary["mday"] - $Ary["wday"] + 1 + 7), $Ary["year"]));
	$Return["Week_N_E"]	= date("Y-m-d", mktime(0, 0, 0, $Ary["mon"], ($Ary["mday"] - $Ary["wday"] + 7 + 7), $Ary["year"]));
	//本月
	$Return["Month_S"]	= date("Y-m-d", mktime(0, 0, 0, $Ary["mon"], 1, $Ary["year"]));
	$Return["Month_E"]	= date("Y-m-d", mktime(0, 0, 0, ($Ary["mon"] + 1), 0, $Ary["year"]));
	//上月
	$Return["Month_L_S"]	= date("Y-m-d", mktime(0, 0, 0, ($Ary["mon"] - 1), 1, $Ary["year"]));
	$Return["Month_L_E"]	= date("Y-m-d", mktime(0, 0, 0, $Ary["mon"], 0, $Ary["year"]));

	return($Return);
}

//檢查是否為正確ㄉ日期
function ChkDate($date){
	$tmp = explode("-", $date);
	return(checkdate($tmp[1], $tmp[2], $tmp[0]));
}

//日期加減
function DateSet($date, $format, $tmp=""){
	$date = date_create($date);
	if($tmp != "")	date_modify($date, $tmp);
	return(date_format($date, $format));
}

//頁數下拉選單
function PageOption($PageNo, $TotalPage){
	$Option = "";
	for ($i=1; $i <= $TotalPage; $i++){ $Option .= "<option value='". $i ."' ". (($i == $PageNo) ? "selected" : "") .">". $i ."</option>"; }
	return($Option);
}

//增加Option
function AddOption($val="",$show="",$flag=""){
	$Option = "<option value='". $val ."' ". (($flag == $val)?"selected":"") ." >". $show ."</option>";
	return($Option);
}


function ip2country($ip){
	$gip = new ip2location;
	$gip->open(WEB_PATH .'IP-COUNTRY.BIN');

	$record = $gip->getAll($ip);
	return $record->countryShort;
}

function getIP() {
	$remoteaddr	= $_SERVER["REMOTE_ADDR"];
	@$xforward	= $_SERVER["HTTP_X_FORWARDED_FOR"];
	if(empty($xforward)){
		return($remoteaddr);
	}else{
		$Tmp = explode(",", $xforward);
		$IP = array_pop($Tmp);
		return $IP;
	}
}

function pre($ary){
	echo("<pre>");
	print_r($ary);
	die("<pre>");
}

function SpanColor($Str, $Color){
	$Str = "<span style='background-color:". $Color .";'>". $Str ."</span>";
	return($Str);
}

function FillZero($val,$len){
	$num = strlen((String)$val);
	if($num < $len) for($i=$num; $i < $len; $i++) $val = "0".$val;
	return $val;
}

function ChkStr($str, $type, $len=0){
	if($type == "N"){	//數字
		if(!is_numeric($str))			return(false);
	}elseif($type == "L"){	//字串
		if($str == "")				return(false);
		if($len != 0 && strlen($str) > $len)	return(false);
		if(preg_match('/[^0-9A-Za-z]/', $str))	return(false);
	}
	return(true);
}
//E-Mail 檢查函數
function ChkEmail($email){
	if(filter_var($email, FILTER_VALIDATE_EMAIL)){
		preg_match('/([^@]+)$/i', $email, $matches);
		if(!function_exists(checkdnsrr))	return(true);
		else if(checkdnsrr($matches[0]))	return(true);
		else					return(false);
	}
	return(false);
}

function get_msec(){
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

//取得 GET OR POST 變數值
function GetVal($Name, $DfVal=""){
	$Val = (isset($_REQUEST[$Name])? $_REQUEST[$Name]:"");
	if(!is_array($Val)){
		$Val = (trim($Val) == "") ? $DfVal : trim($Val);
	}
	return $Val;
}

//數字的處理 千分位, 顏色
function money($num, $p=0){
	if($num == "") $num = 0;
	$num = number_format($num, $p, ".", ",");
	if($num < 0) $num = "<font color='#CC0000'>".$num."</font>";
	return($num);
}

function Curl($Url, $PostData=""){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $Url);		//要抓取的URL
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	//要求結果保存到字符串中還是輸出到屏幕上
	curl_setopt($ch, CURLOPT_HEADER, false);	//是否顯示header信息
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
//	curl_setopt($ch, CURLOPT_TIMEOUT, 10);	// 抓取超时时间
	if($PostData != ""){
		curl_setopt($ch, CURLOPT_POST, 1);		//POST方式發送數據
		curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);//傳遞一個作為HTTP「POST」操作的所有數據的字符串
	}
	$Output = curl_exec($ch);
//	if(!curl_errno($ch)){
//		print_r(curl_getinfo($ch));
//		echo(curl_error($ch));
//	}
	curl_close($ch);
	return($Output);
}

//-------------------------------- JAVASCRIPT FUNCTION --------------------------------
function EorJS($Str, $Url=""){
	$s  = META_CHARSET;
	$s .= "<script language='JavaScript'>\n";
	$s .= "alert ('".$Str."');\n";
	if ($Url == "")	$s .= "window.history.back();\n";
	else		$s .= "window.location.replace('".$Url."');\n";
	$s .= "</script>\n";
	return($s);
}

//執行javascriptㄉ語法
function ExecJS($Str){
	$s  = META_CHARSET;
	$s .= "<script language='JavaScript'>\n";
	$s .= $Str ."\n";
	$s .= "</script>";
	return($s);
}


//執行javascriptㄉ語法
function AlertJS($Str){
	$s  = META_CHARSET;
	$s .= "<script language='JavaScript'>\n";
	$s .= "alert ('".$Str."');\n";
	$s .= "</script>";
	return($s);
}


//新增 修改 ㄉ回上頁
function ExecuteOK($url){
	global $LVar;
	$s  = META_CHARSET;
	$s .= "<script language='JavaScript'>\n";
	$s .= "alert('完成!');\n";
	$s .= "window.location.replace('".$url."');\n";
	$s .= "</script>\n";
	die($s);
}


?>