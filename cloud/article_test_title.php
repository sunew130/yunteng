<?php

require_once(dirname(__FILE__)."/config.php");
AjaxHead();
if(empty($t) || $cfg_check_title=='N') exit;

$row = $dsql->GetOne("SELECT id FROM `#@__archives` WHERE title LIKE '$t' ");
if(is_array($row))
{
    echo "��ʾ��ϵͳ�Ѿ����ڱ���Ϊ '<a href='../yunteng_cc_plus/view.php?aid={$row['id']}' style='color:red' target='_blank'><u>$t</u></a>' ���ĵ���[<a href='#' onclick='javascript:HideObj(\"mytitle\")'>�ر�</a>]";
}