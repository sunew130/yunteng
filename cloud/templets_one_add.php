<?php

require(dirname(__FILE__)."/config.php");
CheckPurview('temp_One');
if(empty($dopost)) $dopost = "";

if($dopost=="save")
{
    require_once(DEDEINC."/arc.partview.class.php");
    $uptime = time();
    $body = str_replace('&quot;', '\\"', $body);
    $filename = preg_replace("#^\/#", "", $nfilename);
    if($likeid=='')
    {
        $likeid = $likeidsel;
    }
    $row = $dsql->GetOne("SELECT filename FROM `#@__sgpage` WHERE likeid='$likeid' AND filename LIKE '$filename' ");
    if(is_array($row))
    {
        ShowMsg("�Ѿ�������ͬ���ļ����������Ϊ�����ļ�����","-1");
        exit();
    }
    $inQuery = "INSERT INTO `#@__sgpage`(title,keywords,description,template,likeid,ismake,filename,uptime,body)
     VALUES('$title','$keywords','$description','$template','$likeid','$ismake','$filename','$uptime','$body'); ";
    if(!$dsql->ExecuteNoneQuery($inQuery))
    {
        ShowMsg("����ҳ��ʧ�ܣ���������Ƿ������⣡","-1");
        exit();
    }
    $id = $dsql->GetLastID();
    include_once(DEDEINC."/arc.sgpage.class.php");
    $sg = new sgpage($id);
    $sg->SaveToHtml();
    ShowMsg("�ɹ�����һ��ҳ�棡","templets_one.php");
    exit();
}
$row = $dsql->GetOne("SELECT MAX(aid) AS aid FROM `#@__sgpage`  ");
$nowid = is_array($row) ? $row['aid']+1 : '';
include_once(DEDEADMIN."/templets/templets_one_add.htm");