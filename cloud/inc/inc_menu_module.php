<?php
require_once(dirname(__FILE__)."/../config.php");

//����ģ��˵�
$moduleset = '';
$dsql->SetQuery("SELECT * FROM `#@__sys_module` ORDER BY id DESC");
$dsql->Execute();
while($row = $dsql->GetObject()) 
{
    $moduleset .= $row->menustring."\r\n";
}

//�������˵�
$plusset = '';
$dsql->SetQuery("SELECT * FROM `#@__plus` WHERE isshow=1 ORDER BY aid ASC");
$dsql->Execute();
while($row = $dsql->GetObject()) {
    $row->menustring = str_replace('plus_��������', 'plus_��������ģ��', $row->menustring);
    $plusset .= $row->menustring."\r\n";
}

$adminMenu = '';
if($cuserLogin->getUserType() >= 10)
{
    $adminMenu = "<m:top name='ģ�����' c='6,' display='block'>
    <m:item name='ģ�����' link='module_main.php' rank='sys_module' target='main' />
    <m:item name='�ϴ���ģ��' link='module_upload.php' rank='sys_module' target='main' />
    <m:item name='ģ��������' link='module_make.php' rank='sys_module' target='main' />
    </m:top>";
}

$menusMoudle = "
-----------------------------------------------
$adminMenu
<m:top item='7' name='�������' display='block'>
  <m:item name='���������' link='plus_main.php' rank='10' target='main' />
  $plusset
</m:top>

$moduleset
-----------------------------------------------
";