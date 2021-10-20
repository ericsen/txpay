<?php
include("../conf/config.php");

$Page	= GetVal("page", 1);
$keyword = GetVal("keyword","");
//die($keyword);
$tpl = new TemplatePower(TPL_PATH ."/addnew.html");
$tpl->includeLang();

if (GetVal("act","")=="checkoid") {
	$vonum = GetVal("vonum","XX");
	$Sql = "SELECT * from Videos WITH(NOLOCK) WHERE vonum like '%" . $vonum . "%'";
	$dbAV->query($Sql);
	if ($dbAV->numRows()==0) {
		die("沒有找到此編號影片!");
	} else {
		$vvv = $dbAV->all_Data();
		foreach($vvv As $vv) {
			echo(">>>找到影片 , vid = " . $vv["vonum"] . " , " . $vv["vtitle"] . "<br>");
	  }
	  die("found");
	}

} else if (GetVal("act","")=="edit") {
	//編輯
		$vid = GetVal("vid","0");
		$Sql = "SELECT * from Videos WITH(NOLOCK) WHERE id= $vid ;";
		//echo($Sql)
		
		$tpl->assignGlobal("ACT","editgo");

		$dbAV->query($Sql);
		$tpl->assign(Array(
			"vid"		=> $dbAV->val("id"),
			"vserver"		=> $dbAV->val("vserver"),
			"vfilename"		=> $dbAV->val("vfilename"),
			"vimage"		=> $dbAV->val("vimage"),
			"vimage1"		=> $dbAV->val("vimage1"),
			"vtitle"		=> $dbAV->val("vtitle"),
			"vrdate"		=> $dbAV->val("vrdate"),
			"vlength"		=> $dbAV->val("vlength"),
			"vmask"		=> $dbAV->val("vmask"),
			"vartist"		=> $dbAV->val("vartist"),
			"vproducer"		=> $dbAV->val("vproducer"),
			"vonum"		=> $dbAV->val("vonum"),
			"vdesc"		=> $dbAV->val("vdesc"),
			"vdesc1"		=> $dbAV->val("vdesc1"),
			"vdesc2"		=> $dbAV->val("vdesc2"),
			"vstatus"		=> $dbAV->val("vstatus"),
			"vcount0"		=> $dbAV->val("vcount0"),
			"vcount1"		=> $dbAV->val("vcount1"),
			"vcount2"		=> $dbAV->val("vcount2"),
			"vsubtype"	=> $dbAV->val("vSubType"),
			"vtotalseries" => $dbAV->val("vtotalseries")
		));
		
		$vtitle = str_replace("'", "''" , $vtitle);
		
		if ($dbAV->val("vDC") != "") $tpl->assign("vtotalseries","1");
		
		
		$sql = "SELECT * from Video2Type WITH(NOLOCK) where vid=$vid";
		$dbAV->query($sql);
		$TDD = $dbAV->all_Data();
		foreach($TDD As $vv) {
	  	$vtd[$vv["vtid"]] = "1";
	  }
		
		$Sql = "SELECT * from VideoType WITH(NOLOCK) ORDER BY vtg asc,vtid ASC;";
		//echo($Sql);
		
		$dbAV->query($Sql);
		
		$TAGS = "";
		$ccc=0;
		$oT="1";
		if($dbAV->Rows > 0){
			$Data = $dbAV->all_Data();
			foreach($Data As $v){
				$ccc++;
				$OO = "";
				if ($vtd[$v["vtid"]]=="1") $OO = "checked";
				$TAGS = $TAGS . "<input type=checkbox $OO name=\"tags[]\" id=\"tags".$v["vtid"]."\" value=" . $v["vtid"] . "><label for=\"tags".$v["vtid"]."\">" . $v["vtnameC"] . "</label> ";
				if ($oT!=$v["vtg"]) $TAGS = $TAGS . "<hr>";
				$oT=$v["vtg"];
			}
			
		}
		
		$tpl->assign ("TAGS",$TAGS);
		
		

} else if (GetVal("act","")=="editgo") {
	//送出修改
	$vid  = GetVal("vid","0");
	$vfilename  = GetVal("vfilename","");
	$vimage     = GetVal("vimage","");
	$vimage1    = GetVal("vimage1","");
	$vtitle     = GetVal("vtitle","");
	$vrdate     = GetVal("vrdate","");
	$vlength    = GetVal("vlength","");
	$vmask      = GetVal("vmask","1");
	$vartist    = GetVal("vartist","");
	$vproducer  = GetVal("vproducer","");
	$vonum      = GetVal("vonum","");
	$vdesc      = htmlspecialchars(GetVal("vdesc",""),ENT_QUOTES);
	$vdesc1     = htmlspecialchars(GetVal("vdesc1",""),ENT_QUOTES);
	$vdesc2     = htmlspecialchars(GetVal("vdesc2",""),ENT_QUOTES);
	$vstatus    = GetVal("vstatus","0");
	$vserver    = GetVal("vserver","0");
	$vsubtype 	= GetVal("vsubtype","");
	$vtotalseries = GetVal("vtotalseries","1");

	$dbAV->query("SELECT * FROM Videos WITH(NOLOCK) WHERE id=$vid");
	$v = $dbAV->val();
	
	$vtitle = str_replace("'", "''" , $vtitle);
	
	if ($v["vDC"]=="")	{	//電影或是大類
		$sql = "UPDATE Videos WITH(ROWLOCK) set ";
		$sql = $sql . "vfilename = N'$vfilename'";
		$sql = $sql . ",vimage = N'$vimage'";
		$sql = $sql . ",vimage1 = N'$vimage1'";
		$sql = $sql . ",vtitle = N'$vtitle'";
		$sql = $sql . ",vrdate = '$vrdate'";
		$sql = $sql . ",vlength = '$vlength'";
		$sql = $sql . ",vmask = '$vmask'";
		$sql = $sql . ",vartist = N'$vartist'";
		$sql = $sql . ",vproducer = N'$vproducer'";
		$sql = $sql . ",vonum = '$vonum'";
		$sql = $sql . ",vdesc = N'$vdesc'";
		$sql = $sql . ",vdesc1 = N'$vdesc1'";
		$sql = $sql . ",vdesc2 = N'$vdesc2'";
		$sql = $sql . ",vstatus = '$vstatus'";
		$sql = $sql . ",vserver = '$vserver'";
		$sql = $sql . ",vsubtype = '$vsubtype'";
		$sql = $sql . ",vtotalseries = '$vtotalseries'";
		$sql = $sql . " WHERE id=$vid";
	} else {	//連續劇的每集
		$sql = "UPDATE Videos WITH(ROWLOCK) set ";
		$sql = $sql . "vfilename = N'$vfilename'";
		$sql = $sql . ",vimage = N'$vimage'";
		$sql = $sql . ",vimage1 = N'$vimage1'";
		$sql = $sql . ",vtitle = N'$vtitle'";
		$sql = $sql . ",vrdate = '$vrdate'";
		$sql = $sql . ",vlength = '$vlength'";
		$sql = $sql . ",vmask = '$vmask'";
		$sql = $sql . ",vartist = N'$vartist'";
		$sql = $sql . ",vproducer = N'$vproducer'";
		$sql = $sql . ",vonum = '$vonum'";
		$sql = $sql . ",vdesc = N'$vdesc'";
		$sql = $sql . ",vdesc1 = N'$vdesc1'";
		$sql = $sql . ",vdesc2 = N'$vdesc2'";
		$sql = $sql . ",vstatus = '$vstatus'";
		$sql = $sql . ",vserver = '$vserver'";
		$sql = $sql . ",vsubtype = '$vsubtype'";
		$sql = $sql . ",vtotalseries = '$vtotalseries'";
		$sql = $sql . " WHERE id=$vid";
	}
	
	$dbAV->query($sql);	
	
	$dbAV->query("DELETE FROM Video2Type WHERE vid=$vid");

	$TAGS = GetVal("tags","");
	$sql = "";
	foreach ($TAGS as $v) {
		$sql = $sql . "INSERT INTO Video2Type WITH(ROWLOCK) (vid,vtid) VALUES ($vid,$v); ";
  }
  if (strlen($sql)>10) {
  	$dbAV->query($sql);
  }
	
	die("修改完成");
} else if (GetVal("submit","")!="") {
	//有新增
	$vid  = GetVal("vid","0");
	$vfilename  = GetVal("vfilename","");
	$vimage     = GetVal("vimage","");
	$vimage1    = GetVal("vimage1","");
	$vtitle     = GetVal("vtitle","");
	$vrdate     = GetVal("vrdate","");
	$vlength    = GetVal("vlength","");
	$vmask      = GetVal("vmask","1");
	$vartist    = GetVal("vartist","");
	$vproducer  = GetVal("vproducer","");
	$vonum      = GetVal("vonum","");
	$vdesc      = htmlspecialchars(GetVal("vdesc",""),ENT_QUOTES);
	$vdesc1     = htmlspecialchars(GetVal("vdesc1",""),ENT_QUOTES);
	$vdesc2     = htmlspecialchars(GetVal("vdesc2",""),ENT_QUOTES);
	$vstatus    = GetVal("vstatus","0");
	$vserver    = GetVal("vserver","0");
	$vsubtype 	= GetVal("vsubtype","");
	$vtotalseries = GetVal("vtotalseries","1");
	
	$dsql = "DELETE FROM Videos where id=$vid or vDC=$vid;";
	
	$vtitle = str_replace("'", "''" , $vtitle);
	
	
	if ($vtotalseries>1) {
		//先新增一笔大类别 , 再把vid填入每一集
		$sql = "INSERT INTO Videos WITH(ROWLOCK) (vfilename,vimage,vimage1,vtitle,vrdate,vlength,vmask,vartist,vproducer,vonum,vdesc,vdesc1,vdesc2,vstatus,vserver,vsubtype,vtotalseries) VALUES ";
		$sql = $sql . "(N'" .$vfilename ."',N'$vimage',N'$vimage1',N'" . $vtitle . " 共".$vtotalseries."','$vrdate','$vlength',$vmask,N'$vartist',N'$vproducer','$vonum',N'$vdesc',N'$vdesc1',N'$vdesc2',$vstatus,$vserver,'$vsubtype',$vtotalseries);";
		$DCvid = $dbAV->insertId($sql);

		for ($i=$vtotalseries ; $i>0 ; $i--) {
			$sql = "INSERT INTO Videos WITH(ROWLOCK) (vfilename,vimage,vimage1,vtitle,vrdate,vlength,vmask,vartist,vproducer,vonum,vdesc,vdesc1,vdesc2,vstatus,vserver,vsubtype,vDC,vtotalseries) VALUES ";
			$sql = $sql . "(N'" .$vfilename ."/$i.mp4',N'$vimage',N'$vimage1',N'" . $vtitle . " ($i/$vtotalseries)','$vrdate','$vlength',$vmask,N'$vartist',N'$vproducer','$vonum',N'($i/$vtotalseries) $vdesc',N'($i/$vtotalseries) $vdesc1',N'($i/$vtotalseries) $vdesc2',1,$vserver,'$vsubtype',$DCvid,$i);";
			$vid = $dbAV->insertId($sql);
		}
	} else {
		$sql = "INSERT INTO Videos WITH(ROWLOCK) (vfilename,vimage,vimage1,vtitle,vrdate,vlength,vmask,vartist,vproducer,vonum,vdesc,vdesc1,vdesc2,vstatus,vserver,vsubtype) VALUES ";
		$sql = $sql . "(N'$vfilename',N'$vimage',N'$vimage1',N'$vtitle','$vrdate','$vlength',$vmask,N'$vartist',N'$vproducer','$vonum',N'$vdesc',N'$vdesc1',N'$vdesc2',$vstatus,$vserver,'$vsubtype');";
		$vid = $dbAV->insertId($sql);
		
	}

	$dbAV->query($dsql);	//砍了旧的
	
	$TAGS = GetVal("tags","");

	$sql = "";
	foreach ($TAGS as $v) {
		$sql = $sql . "INSERT INTO Video2Type WITH(ROWLOCK) (vid,vtid) VALUES ($vid,$v); ";
  }
  if (strlen($sql)>10) {
  	$dbAV->query($sql);
  }
	
	die("新增完成<br>$vimage");
} else {
		//新增
		$Sql = "SELECT * from VideoType WITH(NOLOCK) ORDER BY vtg asc,vtid ASC;";
		//echo($Sql);
		
		$dbAV->query($Sql);
		
		$TAGS = "";
		$ccc=0;
		$oT="1";
		if($dbAV->Rows > 0){
			$Data = $dbAV->all_Data();
			foreach($Data As $v){
				$ccc++;
				$TAGS = $TAGS . "<input type=checkbox name=\"tags[]\" id=\"tags".$v["vtid"]."\" value=".$v["vtid"]."><label for=\"tags" . $v["vtid"] . "\">" . $v["vtnameC"] . "</label> ";
				if ($oT!=$v["vtg"]) $TAGS = $TAGS . "<hr>";
				$oT=$v["vtg"];
			}
			
		}
		
		$tpl->assign("TAGS",$TAGS);
		$tpl->assign("vstatus","0");	//預設不啟用
		$tpl->assign("vmask","0");
	
}

$tpl->printToScreen();
?>

