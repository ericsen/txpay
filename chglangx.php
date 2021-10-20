<?php
//更換語系
$NotLogin = 'Y';
include("../conf/config.php");

switch($_GET["langx"]){
	case "zh-tw":	$_SESSION["S_LS"] = "C";	$_SESSION["S_Langx"] = "zh-tw";	break;	//繁體
	case "zh-cn":	$_SESSION["S_LS"] = "G";	$_SESSION["S_Langx"] = "zh-cn";	break;	//簡體
	case "en-us":	$_SESSION["S_LS"] = "E";	$_SESSION["S_Langx"] = "en-us";	break;	//英文
	case "th-th":	$_SESSION["S_LS"] = "T";	$_SESSION["S_Langx"] = "th-th";	break;	//泰文
	case "vi-vn":	$_SESSION["S_LS"] = "V";	$_SESSION["S_Langx"] = "vi-vn";	break;	//越文
	case "ja-jp":	$_SESSION["S_LS"] = "J";	$_SESSION["S_Langx"] = "ja-jp";	break;	//日文
	case "ko-kr":	$_SESSION["S_LS"] = "K";	$_SESSION["S_Langx"] = "ko-kr";	break;	//韓文
	default:	$_SESSION["S_LS"] = "C";	$_SESSION["S_Langx"] = "zh-tw";	break;
}

die("top.location.reload();");
?>