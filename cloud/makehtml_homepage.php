<?php

require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_MakeHtml');
require_once(DEDEINC."/arc.partview.class.php");
if(empty($dopost)) $dopost = '';

if($dopost=="view")
{
    $pv = new PartView();
    $templet = str_replace("{style}",$cfg_df_style,$templet);
    $pv->SetTemplet($cfg_basedir.$cfg_templets_dir."/".$templet);
    $pv->Display();
    exit();
}
else if($dopost=="make")
{
    $remotepos = empty($remotepos)? '/index.html' : $remotepos;
    $isremote = empty($isremote)? 0 : $isremote;
    $serviterm = empty($serviterm)? "" : $serviterm;
    $homeFile = DEDEADMIN."/".$position;
    $homeFile = str_replace("\\","/",$homeFile);
    $homeFile = str_replace("//","/",$homeFile);
    $fp = fopen($homeFile,"w") or die("��ָ�����ļ��������⣬�޷������ļ�");
    fclose($fp);
    if($saveset==1)
    {
        $iquery = "UPDATE `#@__homepageset` SET templet='$templet',position='$position' ";
        $dsql->ExecuteNoneQuery($iquery);
    }

    if ($showmod == 1)
    {

        $templet = str_replace("{style}", $cfg_df_style, $templet);
        $pv = new PartView();
        $GLOBALS['_arclistEnv'] = 'index';
        $pv->SetTemplet($cfg_basedir.$cfg_templets_dir."/".$templet);
        $pv->SaveToHtml($homeFile);
        echo "<span style='color:#333;font-family:΢���ź�; font-size:13px; '>�ɹ�������ҳHTML��".$homeFile."</span>&nbsp;&nbsp;<a href='{$position}' target='_blank' style='color:#ff0000;font-family:΢���ź�; '>���...</a><br />";
    } else { 

        if (file_exists($homeFile)) @unlink($homeFile);
        echo "���ö�̬���ģʽ��<a href='../index.php' target='_blank'>���...</a><br />";
    }
    
    $iquery = "UPDATE `#@__homepageset` SET showmod='$showmod'";
    $dsql->ExecuteNoneQuery($iquery);

    if($serviterm =="")
    {
        $config=array();
    } else {
        list($servurl, $servuser, $servpwd) = explode(',',$serviterm);
        $config=array( 'hostname' => $servurl, 'username' => $servuser, 
                       'password' => $servpwd,'debug' => 'TRUE');
    }

    if($cfg_remote_site=='Y' && $showmod==1)
    {
        if($ftp->connect($config) && $isremote == 1)
        {
            if($ftp->upload($position, $remotepos, 'ascii')) echo "Զ�̷����ɹ�!"."<br />";
        }
    }
    exit();
}
$row  = $dsql->GetOne("SELECT * FROM #@__homepageset");
include DedeInclude('templets/makehtml_homepage.htm');