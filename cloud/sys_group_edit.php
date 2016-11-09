<?php

require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Group');
if(empty($dopost)) $dopost = "";

if($dopost=='save')
{
    if($rank==10)
    {
        ShowMsg('超级管理员的权限不允许更改!', 'sys_group.php');
        exit();
    }
    $purview = "";
    if(is_array($purviews))
    {
        foreach($purviews as $p)
        {
            $purview .= "$p ";
        }
        $purview = trim($purview);
    }
    $dsql->ExecuteNoneQuery("UPDATE `#@__admintype` SET typename='$typename',purviews='$purview' WHERE CONCAT(`rank`)='$rank'");
    ShowMsg('成功更改用户组的权限!', 'sys_group.php');
    exit();
}
else if($dopost=='del')
{
    $dsql->ExecuteNoneQuery("DELETE FROM `#@__admintype` WHERE CONCAT(`rank`)='$rank' AND system='0';");
    ShowMsg("成功删除一个用户组!","sys_group.php");
    exit();
}
$groupRanks = Array();
$groupSet = $dsql->GetOne("SELECT * FROM `#@__admintype` WHERE CONCAT(`rank`)='{$rank}' ");
$groupRanks = explode(' ', $groupSet['purviews']);
include DedeInclude('templets/sys_group_edit.htm');

function CRank($n)
{
    global $groupRanks;
    return in_array($n,$groupRanks) ? ' checked' : '';
}