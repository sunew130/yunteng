<?php
require_once(dirname(__FILE__).'/../../include/common.inc.php');

$dsql->ExecuteNoneQuery("Update `#@__sys_task` set sta='�ɹ�' where dourl='dede-optimize-table.php' ");
echo "Welcome to www.yunteng.cc!";
exit();
?>