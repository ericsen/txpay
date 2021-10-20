<?php
include("../conf/config.php");

define("CURL_TIMEOUT",   10); 
define("URL",            "http://api.fanyi.baidu.com/api/trans/vip/translate"); 
define("APP_ID",         "20171229000110250"); //替换为您的APPID
define("SEC_KEY",        "FaECMWRXZKFqU7xm0Dfj");//替换为您的密钥

$dbAV->query("select * from Videos where vSubtype='JP' and vcount2<>999 order by id desc");
$Data = $dbAV->all_Data();
foreach($Data As $v) {
	$tt = translate($v["vtitle"],'auto','zh');
	$tt = str_replace("'","''",$tt["trans_result"][0]["dst"]);
	
	$dd = $v["vdesc"];
	$dd = str_replace (array("\r\n", "\n", "\r"), '|', $dd);
	$td = translate($dd,'auto','zh');
	$td = str_replace("|","<br/>",$td["trans_result"][0]["dst"]);
	$td = str_replace("'","''",$td);
	
	$dbAV->query("UPDATE Videos WITH(ROWLOCK) set vcount2=999, vtitle=N'" . $tt . "' , vdesc1=N'" . $td . "' where id=".$v["id"]);
	echo($v["id"] . "<br>");
}





//翻译入口
function translate($query, $from="auto", $to="zh")
{
    $args = array(
        'q' => $query,
        'appid' => APP_ID,
        'salt' => rand(10000,99999),
        'from' => $from,
        'to' => $to,

    );
    $args['sign'] = buildSign($query, APP_ID, $args['salt'], SEC_KEY);
    $ret = call(URL, $args);
    $ret = json_decode($ret, true);
    return $ret; 
}

//加密
function buildSign($query, $appID, $salt, $secKey)
{/*{{{*/
    $str = $appID . $query . $salt . $secKey;
    $ret = md5($str);
    return $ret;
}/*}}}*/

//发起网络请求
function call($url, $args=null, $method="post", $testflag = 0, $timeout = CURL_TIMEOUT, $headers=array())
{/*{{{*/
    $ret = false;
    $i = 0; 
    while($ret === false) 
    {
        if($i > 1)
            break;
        if($i > 0) 
        {
            sleep(1);
        }
        $ret = callOnce($url, $args, $method, false, $timeout, $headers);
        $i++;
    }
    return $ret;
}/*}}}*/

function callOnce($url, $args=null, $method="post", $withCookie = false, $timeout = CURL_TIMEOUT, $headers=array())
{/*{{{*/
    $ch = curl_init();
    if($method == "post") 
    {
        $data = convert($args);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_POST, 1);
    }
    else 
    {
        $data = convert($args);
        if($data) 
        {
            if(stripos($url, "?") > 0) 
            {
                $url .= "&$data";
            }
            else 
            {
                $url .= "?$data";
            }
        }
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if(!empty($headers)) 
    {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if($withCookie)
    {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $_COOKIE);
    }
    $r = curl_exec($ch);
    curl_close($ch);
    return $r;
}/*}}}*/

function convert(&$args)
{/*{{{*/
    $data = '';
    if (is_array($args))
    {
        foreach ($args as $key=>$val)
        {
            if (is_array($val))
            {
                foreach ($val as $k=>$v)
                {
                    $data .= $key.'['.$k.']='.rawurlencode($v).'&';
                }
            }
            else
            {
                $data .="$key=".rawurlencode($val)."&";
            }
        }
        return trim($data, "&");
    }
    return $args;
}/*}}}*/

?>
