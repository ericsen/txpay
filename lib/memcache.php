<?php
/*
選項 	說明
add 	僅當存儲空間中不存在鍵相同的數據時才保存
replace 僅當存儲空間中存在鍵相同的數據時才保存
set 	與add和replace不同，無論何時都保存
*/

class Cache {
	var $Conn = null;

	public function __construct($CacheAry) {
		foreach($CacheAry AS $v){
			list($host, $port) = explode(":", $v);
			if($this->Conn == null)	$this->Conn = memcache_connect($host, $port);
			else			memcache_add_server($this->Conn, $host, $port);
		}
	}

	//儲存 一筆資料  已經存在 回傳 false
	public function Add($key, $val, $expire=0){
		return @memcache_set($this->Conn, $key, $val, 0, $expire);
	}

	//儲存 一筆資料  已經存在 覆寫
	public function Set($key, $val, $expire=0){
		return @memcache_set($this->Conn, $key, $val, 0, $expire);
	}

	//針對 已經存在ㄉ資料 覆寫
	public function Replace($key, $val, $expire=0){
		return @memcache_replace($this->Conn, $key, $val, 0, $expire);
	}

	//取出 資料 ($key 可以是 array)
	public function Get($key){
		return @memcache_get($this->Conn, $key);
	}

	//刪除一筆資料
	public function Delete($key){
		return @memcache_delete($this->Conn, $key, -1);
	}

	//清除 暫存 資料
	public function Flush(){
		return @memcache_flush($this->Conn);
	}

	//關閉連線
	public function Close(){
		return @memcache_close($this->Conn);
	}
}
?>