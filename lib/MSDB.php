<?php
class JCDB extends DB {
	var $MaxRows   = 20;  		//一頁顯示幾筆資料  預設為 20 筆
	var $TotalMun  = 0;		//資料ㄉ總筆數
	var $TotalPage = 1;		//資料ㄉ總頁數
	var $PageNo = 1;		//目前ㄉ頁數

	function JCDB($host, $dbname, $user, $pwd) {	//初始化
		$this->Host	= $host;
		$this->DbName	= $dbname;
		$this->User	= $user;
		$this->Pwd	= $pwd;
	}

	//分頁顯示資料
	function queryPage($Sql){
		$TmpSql = str_replace(";", "", $Sql);

		$Sql_OrderBy	= stristr($TmpSql, "ORDER BY");			//擷取 ORDER BY 後面ㄉ字串 含 ORDER BY
		$TmpSql		= str_replace($Sql_OrderBy, "", $TmpSql);	//去除 ORDER BY 以後ㄉ字串
		$MunSql		= $TmpSql;					//讀取總共幾筆資料ㄉSQL
		$Sql_Where	= stristr($TmpSql, "WHERE");			//擷取 WHERE 後面ㄉ字串 含 FROM
		$TmpSql		= str_replace($Sql_Where, "", $TmpSql);		//去除 WHERE 以後ㄉ字串
		$Sql_From	= stristr($TmpSql, "FROM ");			//擷取 FROM 後面ㄉ字串 含 FROM
		$TmpSql		= str_replace($Sql_From, "", $TmpSql);		//去除 FROM 以後ㄉ字串
		$Sql_Select	= $TmpSql;					//最後剩下 SELECT 後面ㄉ字串 含 SELECT

		//取得總筆數
//		$this->query("SELECT COUNT(*) AS Mun ". $Sql_From ." ". $Sql_Where);
		$this->query("SELECT COUNT(*) AS Mun FROM (". $MunSql .") AS a;");
		$this->TotalMun =$this->val('Mun');

		//計算總頁數
		if($this->TotalMun == 0)	$this->TotalPage = 1;
		else				$this->TotalPage = ceil($this->TotalMun / $this->MaxRows);

		//如果選擇ㄉ頁數超過最後一頁，則顯示最後一頁
		if($this->PageNo > $this->TotalPage)	$this->PageNo = $this->TotalPage;

		$Between_S = ($this->PageNo - 1) * $this->MaxRows + 1;
		$Between_E = $this->PageNo * $this->MaxRows;
		$this->query($Sql1 = "SELECT * FROM (". $Sql_Select .", ROW_NUMBER() OVER (". $Sql_OrderBy .") AS RowNum ". $Sql_From ." ". $Sql_Where .") TmpTab WHERE RowNum BETWEEN ". $Between_S ." AND ". $Between_E .";");
	}

	//新增並取得新增id
	function insertId($Sql){
		$A = substr($Sql, -1);
		if($A == ";")	$Sql .= "  SELECT SCOPE_IDENTITY() AS Expr1;";
		else		$Sql .= "; SELECT SCOPE_IDENTITY() AS Expr1;";
		$this->query($Sql);
		return $this->val("Expr1");
  	}

  	function val($key = ""){
  		if($key == "")	return($this->Data);
  		else		return(stripslashes($this->Data[$key]));
   	}

  	function all_Data($qfield=''){
  		$alldata = array();
  		if($this->Rows == 0)	return($alldata);

  		if(empty($qfield)){
  			do{
				$alldata[] = $this->Data;
			}while($this->Data = $this->fetchAssoc() );
  		}else{
			do{
				$alldata[$this->Data[$qfield]] = $this->Data;
			}while($this->Data = $this->fetchAssoc() );
  		}
		return($alldata);
   	}

   	function nextRow(){
   		if($this->Data = $this->fetchAssoc()) 	return(true);
   		else  					return(false);
   	}
}


class DB {
	// Connection parameters
	var $Host	= '';
	var $User	= '';
	var $Pwd	= '';
	var $DbName	= '';
	var $Persistent	= false;
	var $Data	= array();
	var $Rows	= 0;
	var $Conn	= NULL;	// Database connection handle
	var $Result	= false; // Query result

	function DB($host, $dbname, $user, $pwd) {
		//載入 Connection parameters
		$this->Host	= $host;
		$this->Dbname	= $dbname;
		$this->User	= $user;
		$this->Pwd	= $pwd;
	}

	function open() {
	  	// 選擇 MsSql 的連線方式
	  	if($this->Persistent)	$this->Conn = mssql_pconnect($this->Host, $this->User, $this->Pwd);	//建立一個持續性的連線
	  	else			$this->Conn = mssql_connect($this->Host, $this->User, $this->Pwd);	//建立一個即時的的連線，用完即丟
	 	if(!$this->Conn)	return false;

	  	//選擇 database
	  	if(!@mssql_select_db($this->DbName, $this->Conn))	return false;
		return true;
	}

	//關閉連線
	function close() {
	  	$this->freeResult();
	  	mssql_close($this->Conn);
	}

	//錯誤訊息 最後依各訊息
	function error() {
	  	return (@mssql_get_last_message());
	}

	//傳送MS SQL查詢
	function query($sql = '') {
		if(!$this->Conn){
			if (!$this->open()) 	die($this->error());
		}
		$this->Data = array();
		$this->Rows = 0;

		$this->Result = @mssql_query($sql, $this->Conn);
		$this->Rows = $this->numRows();

		if($this->Rows > 0)	$this->Data = $this->fetchAssoc();

		if($this->Result != false)	return ($this->Result);
		elseif($this->error() != "")	{
			@file_put_contents("/tmp/sql_error.log" , Date("Y-m-d H:i:s")." -> ".$this->error()."<br>".$sql."\n", FILE_APPEND );
			
			die($this->error()."<br>".$sql);
		}
	}

	//傳送MS SQL查詢 , 遇到錯誤 throw exception
	function queryex($sql = '') {
		if(!$this->Conn){
			if (!$this->open()) 	die($this->error());
		}
		$this->Data = array();
		$this->Rows = 0;

		$this->Result = @mssql_query($sql, $this->Conn);
		$this->Rows = $this->numRows();

		if($this->Rows > 0)	$this->Data = $this->fetchAssoc();

		if($this->Result != false)	return ($this->Result);
		elseif($this->error() != "")	throw new Exception($this->error()."<br>".$sql);
	}

	//取得資料筆數
	function numRows() {
	   	return (@mssql_num_rows($this->Result));
	}

	// 在一個MS SQL server 資料庫上執行一個預存程序
	function wxecute($sql = '') {
	   	return (@mssql_execute($sql));
	}

	function fetchObject() {
	  	return (@mssql_fetch_object($this->Result, MSSQL_ASSOC));
	}

	function fetchArray() {
    		return (@mssql_fetch_array($this->Result, MSSQL_NUM));
	}

	function fetchAssoc() {
		return (@mssql_fetch_assoc($this->Result));
	}

	function dataSeek($tmp) {
		if(!@mssql_data_seek($this->Result, $tmp))	return false;
	}

	//清出結果記憶體
	function freeResult() {
		mssql_free_result($this->Result);
	}
}
?>