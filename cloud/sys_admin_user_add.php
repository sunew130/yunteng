<?php

require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_User');
require_once(DEDEINC."/typelink.class.php");
if(empty($dopost)) $dopost='';

if($dopost=='add')
{
    if(preg_match("#[^0-9a-zA-Z_@!\.-]#", $pwd) || preg_match("#[^0-9a-zA-Z_@!\.-]#", $userid))
    {
        ShowMsg('�������û������Ϸ���<br />��ʹ��[0-9a-zA-Z_@!.-]�ڵ��ַ���', '-1', 0, 3000);
        exit();
    }
    $safecodeok = substr(md5($cfg_cookie_encode.$randcode), 0, 24);
    if($safecode != $safecodeok )
    {
        ShowMsg('����д��ȫ��֤����', '-1', 0, 3000);
        exit();
    }

    $inquery = "INSERT INTO `#@__admin`(id,usertype,userid,pwd,uname,typeid,tname,email)
                                                    VALUES('$mid','$usertype','$userid','$pwd','$uname','$typeid','$tname','$email'); ";
    $rs = $dsql->ExecuteNoneQuery($inquery);

    ShowMsg('�ɹ�����һ���û���', 'sys_admin_user.php');
    exit();
}
$randcode = mt_rand(10000, 99999);
$safecode = substr(md5($cfg_cookie_encode.$randcode), 0, 24);
$typeOptions = '';
$dsql->SetQuery(" SELECT id,typename FROM `#@__arctype` WHERE reid=0 AND (ispart=0 OR ispart=1) ");
$dsql->Execute('op');
while($row = $dsql->GetObject('op'))
{
    $topc = $row->id;
    $typeOptions .= "<option value='{$row->id}' class='btype'>{$row->typename}</option>\r\n";
    $dsql->SetQuery(" SELECT id,typename FROM `#@__arctype` WHERE reid={$row->id} AND (ispart=0 OR ispart=1) ");
    $dsql->Execute('s');
    while($row = $dsql->GetObject('s'))
    {
        $typeOptions .= "<option value='{$row->id}' class='stype'>��{$row->typename}</option>\r\n";
    }
}
include DedeInclude('templets/sys_admin_user_add.htm');