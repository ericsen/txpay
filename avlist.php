<?php
include("../conf/config.php");

$Page = GetVal("page", 1);
$sdate = GetVal("sdate","");
$edate = GetVal("edate","");
$keyword = GetVal("keyword","");
$act = GetVal("act","");
$vid = GetVal("vid","0");

$tpl = new TemplatePower(TPL_PATH ."/avlist.html");
$tpl->includeLang();

if ($act!="") {
  //有操作功能
  if ($act=="delete") {
      //刪除 Videos
      //刪除 Video2Type 關連
      $sql = "DELETE FROM Videos where id = " . $vid . ";";
      $sql = $sql . "DELETE FROM Video2Type where vid = " . $vid . ";";
      $dbAV->query($sql);
      die("刪除完成");
  } else if ($act=="enable") {
      $sql = "UPDATE Videos WITH(ROWLOCK) set vstatus=1 where id=" . $vid;
      $dbAV->query($sql);
      header("avlist.php");
  } else if ($act=="disable") {
      $sql = "UPDATE Videos WITH(ROWLOCK) set vstatus=0 where id=" . $vid;
      $dbAV->query($sql);
      header("avlist.php");
  } else if ($act=="修改") {
  }
}



$subsql = "";

$filter = Array();
if ($keyword != "") {
  $tpl->assign("keyword",$keyword);
  
  if (GetVal("submit1")=="搜尋ID") {
  	array_push($filter, "(ID=$keyword)");
  } else {
	  array_push($filter, "(vfilename like N'%" . $keyword . "%' or vimage like N'%" . $keyword . "%' or vtitle like N'%" . $keyword . "%' or vproducer like N'%" . $keyword . "%' or vfilename like N'%" . $keyword . "%' or vonum like '%" . $keyword . "%' or vdesc like N'%" . $keyword . "%' or vdesc1 like N'%" . $keyword . "%' or vdesc2 like N'%" . $keyword . "%' )");
	}
}

if ($sdate != '') {
  $tpl->assign('sdate', $sdate);
  array_push($filter, "AddDate >= '" . $sdate . " 00:00:00'");
}

if ($edate != '') {
  $tpl->assign('edate', $edate);
  array_push($filter, "AddDate <= '" . $edate . " 23:59:59'");
}

if (sizeof($filter) > 0) {
  $subsql = ' WHERE ' . implode(' AND ', $filter) . ' ';
}

$Sql = "SELECT * from VideoType WITH(NOLOCK)";
$dbAV->query($Sql);

$VT="";
if($dbAV->Rows > 0){
  do{
    $VT[$dbAV->val("vtid")] = $dbAV->val("vtnameC");
  }while($dbAV->nextRow());
}


$Sql = "SELECT * from Videos WITH(NOLOCK) " . $subsql . " ORDER BY vstatus asc, ID DESC;";
//echo($Sql);

$dbAV->MaxRows = 30;
$dbAV->PageNo  = $Page;
$dbAV->queryPage($Sql);
$tpl->assign(Array(
  "PAGE_OPTION" => PageOption($dbAV->PageNo, $dbAV->TotalPage),
  "PAGE_TOTAL"  => $dbAV->TotalPage,
));

if($dbAV->Rows > 0){
  $Data = $dbAV->all_Data();

  foreach($Data As $v){
    $tpl->newBlock("Data");

    $SQL = "SELECT * from Video2Type WITH(NOLOCK) where vid=" . $v["id"];
    $dbAV->query($SQL);
    $TAGS = "";
    if($dbAV->Rows > 0){
      do{
        $TAGS = $TAGS . $VT[$dbAV->val("vtid")] . "<br>";
      }while($dbAV->nextRow());
    }

    $vserverip = $avip[$v["vserver"]];
    $fname = md5(Date("ymd") . $v["id"]) . ".f4v";

    @unlink( "/home/autofs1/video/" . $fname );

    //2015-01-7 Kirk 換成 NAS 單一儲存
    if (!file_exists("/home/autofs1/video/" . $fname)) {
      //這裡會產生找不到檔案的錯誤 , 因為本機上沒有 /home/video 的目錄,  但是每台 video server 上面都有 , 所以產生link 就好 , 不需要理會錯誤
      @symlink("/autofs/av/" . $v["vfilename"] ,"/home/autofs1/video/" . $fname);
    }

    $tpl->assign(Array(
      "ID"    => $v["id"],
      "vsubtype"   => $LVar["VST_" . $v["vSubType"]],
      "vserverip"   => $vserverip,
      "vfilename"   => $v["vfilename"],
      "vfilename_hash" => $fname,
      "vimage"    => $v["vimage"],
      "vimage1"   => $v["vimage1"],
      "vtitle"    => $v["vtitle"],
      "vrdate"    => $v["vrdate"],
      "vadate"    => $v["AddDate"],
      "vlength"   => $v["vlength"],
      "vmask"   => $v["vmask"],
      "vartist"   => $v["vartist"],
      "vproducer"   => $v["vproducer"],
      "vonum"   => $v["vonum"],
      "vdesc"   => $v["vdesc"],
      "vdesc1"    => $v["vdesc1"],
      "vdesc2"    => $v["vdesc2"],
      "vstatus"   => $v["vstatus"]=="1" ? "啟用":"<font color=red>停用</font>" ,
      "vcount0"   => $v["vcount0"],
      "vcount1"   => $v["vcount1"],
      "vcount2"   => $v["vcount2"],
      "TAGS"  => $TAGS,
    ));
    
    if ($v["vSubType"]=="JP") {
    	$tpl->assign("VPHOTO","//lv5252.com/avphoto/" . $v["vimage"]);
    	$tpl->assign("VMOVIE","");   	
    } else {
    	$tpl->assign("VPHOTO","/VIDEOS/photo/" . $v["vimage"]);
    	$tpl->assign("VMOVIE","/VIDEOS/video/" . $v["vfilename"]);
    }
    
    
  }
  $tpl->assignGlobal(Array(
    "VSERVERIP" => $avip[1]
  ));
}else{
  $tpl->newBlock("NoData");
}
$tpl->assignGlobal("DATE",date('YmdHn'));
$tpl->printToScreen();
?>

