<?php

require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_ArcBatch');
if(empty($dopost)) $dopost = '';
if(empty($step)) $step = 1;

if($dopost=="ok")
{
    if(empty($uparc)) $uparc = 0;
    if($step == -1)
    {
        if($uparc == 0) sleep(1);
        ShowMsg("<span style='color:#333;font-family:΢���ź�; color:#ff0000;'>�ɹ��������л��棡</span>","javascript:;");
        exit();
    }

    else if($step == 1)
    {
        UpDateCatCache();
        ClearOptCache();
        ShowMsg("<span style='color:#333;font-family:΢���ź�; '>�ɹ�������Ŀ���棬����̨��Ŀѡ��,׼������ö�ٻ���...</span>","sys_cache_up.php?dopost=ok&step=2&uparc=$uparc");
        exit();
    }

    else if($step == 2)
    {
        include_once(DEDEINC."/enums.func.php");
        WriteEnumsCache();

        ShowMsg("<span style='color:#333;font-family:΢���ź�; '>�ɹ�����ö�ٻ��棬׼�����µ��û���...</span>", "sys_cache_up.php?dopost=ok&step=3&uparc=$uparc");
        exit();
    }

    else if($step == 3)
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=gb2312'.$cfg_soft_lang.'">';
        $dsql->ExecuteNoneQuery("DELETE FROM `#@__arccache`");
        echo "\n<span style='color:#333;font-family:΢���ź�; font-size:12px; '>�ɹ�����arclist���û��棬׼��������ڻ�Ա������ʷ...</span><br /><br />";
        $oldtime = time() - (90 * 24 * 3600);
        $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_vhistory` WHERE vtime<'$oldtime' ");
        echo "<span style='color:#333;font-family:΢���ź�;font-size:12px; '>�ɹ�������ڻ�Ա������ʷ��׼��������ڶ���...</span><br /><br />";
        $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_pms` WHERE sendtime<'$oldtime' ");
        echo "<span style='color:#333;font-family:΢���ź�; font-size:12px; '>�ɹ�������ڶ��ţ�׼�����������ĵ��������Ҫռ�ϳ���ʱ��...</span>";
        if($uparc == 1)
        {
            echo "<script language='javascript'>location='sys_cache_up.php?dopost=ok&step=9';</script>";
        }
        else
        {
            echo "<script language='javascript'>location='sys_cache_up.php?dopost=ok&step=-1&uparc=$uparc';</script>";
        }
        exit();
    }

    else if($step == 9)
    {
        ShowMsg('���������ĵ������Ѿ�ȡ��������&lt;ϵͳ-&gt;ϵͳ�����޸�[S]&gt;�в���...','sys_cache_up.php?dopost=ok&step=-1&uparc=1',0,5000);
      exit();
    }
}
include DedeInclude('templets/sys_cache_up.htm');