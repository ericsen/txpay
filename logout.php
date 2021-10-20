<?php
include("../conf/config.php");

if($_SESSION["CGUID"] != "") {
	$db->query("DELETE FROM LoginAdmin WITH (ROWLOCK) WHERE CGUID = '". $_SESSION["CGUID"] ."';");
}
session_unset();
session_destroy();

$Str  = "alert('". $LVar["LogoutOK"] ."');\n";
$Str .= "top.location.replace('". WEB_HOST ."');\n";
die(ExecJS($Str));
?>