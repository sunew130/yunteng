<?php

require_once(dirname(__FILE__)."/config.php");
CheckPurview('temp_One');
require_once(DEDEINC."/datalistcp.class.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");

$addquery = '';
$keyword = (!isset($keyword) ? '' : $keyword);
$likeid = (!isset($likeid) ? '' : $likeid);
$addq = $likeid!='' ? " AND likeid LIKE '$likeid' " : '';
$sql = "SELECT aid,title,ismake,uptime,filename,likeid FROM `#@__sgpage` WHERE title LIKE '%$keyword%' $addq ORDER BY aid DESC";
$dlist = new DataListCP();
$dlist->SetTemplet(DEDEADMIN."/templets/templets_one.htm");
$dlist->SetSource($sql);
$dlist->display();

function GetIsMake($im)
{
    return $im==1 ? '�����' : '������';
}