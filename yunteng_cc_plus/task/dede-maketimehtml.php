<?php
require_once(dirname(__FILE__).'/../../include/common.inc.php');

$dsql->ExecuteNoneQuery("Update `#@__sys_task` set sta='�ɹ�' where dourl='dede-maketimehtml.php' ");
echo "Welcome to www.yunteng.cc!";
exit();
?>