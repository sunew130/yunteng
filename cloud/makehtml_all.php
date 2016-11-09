<?php
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/channelunit.func.php");
$action = (empty($action) ? '' : $action);

if($action=='')
{
    require_once(DEDEADMIN."/templets/makehtml_all.htm");
    exit();
}
else if($action=='make')
{
    if(empty($step)) $step = 1;

    if($step==1)
    {
        $starttime = GetMkTime($starttime);
        $mkvalue = ($uptype=='time' ? $starttime : $startid);
        OptimizeData($dsql);
        ShowMsg("<span style='color:#333;font-family:΢���ź�;font-size:12px; '>��������Ż������ڿ�ʼ�����ĵ���</span>","makehtml_all.php?action=make&step=2&uptype=$uptype&mkvalue=$mkvalue");
        exit();
    }

    else if($step==2)
    {
        include_once(DEDEADMIN."/makehtml_archives_action.php");
        exit();
    }

    if($step==3)
    {
        include_once(DEDEINC."/arc.partview.class.php");
        $pv = new PartView();
        $row = $pv->dsql->GetOne("SELECT * FROM `#@__homepageset` ");
		$templet = str_replace("{style}", $cfg_df_style,$row['templet']);
		$homeFile = DEDEADMIN.'/'.$row['position'];
		$homeFile = str_replace("\\", '/', $homeFile);
		$homeFile = preg_replace("#\/{1,}#" ,'/', $homeFile);
		if($row['showmod'] == 1)
		{
			$pv->SetTemplet($cfg_basedir.$cfg_templets_dir.'/'.$templet);
			$pv->SaveToHtml($homeFile);
			$pv->Close();
		} else {
			if (file_exists($homeFile)) @unlink($homeFile);
		}
        ShowMsg("<span style='color:#333;font-family:΢���ź�;font-size:12px; '>��ɸ��������ĵ������ڿ�ʼ������Ŀҳ��</span>","makehtml_all.php?action=make&step=4&uptype=$uptype&mkvalue=$mkvalue");
        exit();
    }

    else if($step==4)
    {
        $mkvalue = intval($mkvalue);
        $typeidsok = $typeids = array();
        $adminID = $cuserLogin->getUserID();
        $mkcachefile = DEDEDATA."/mkall_cache_{$adminID}.php";
        if($uptype=='all' || empty($mkvalue))
        {
            ShowMsg("<span style='color:#333;font-family:΢���ź�;font-size:12px; '>����Ҫ���г��������ָ���������Ŀ��</span>", "makehtml_list_action.php?gotype=mkallct");
            exit();
        }
        else
        {
            if($uptype=='time')
            {
                $query = "SELECT  DISTINCT typeid From `#@__arctiny` WHERE senddate >=".GetMkTime($mkvalue)." AND arcrank>-1";
            }
            else
            {
                $query = "SELECT DISTINCT typeid From `#@__arctiny` WHERE id>=$mkvalue AND arcrank>-1";
            }
            $dsql->SetQuery($query);
            $dsql->Execute();
            while($row = $dsql->GetArray())
            {
                $typeids[$row['typeid']] = 1;
            }

            foreach($typeids as $k=>$v)
            {
                $vs = array();
                $vs = GetParentIds($k);
                if( !isset($typeidsok[$k]) )
                {
                    $typeidsok[$k] = 1;
                }
                foreach($vs as $k=>$v)
                {
                    if(!isset($typeidsok[$v]))
                    {
                        $typeidsok[$v] = 1;
                    }
                }
            }
        }
        $fp = fopen($mkcachefile,'w') or die("�޷�д�뻺���ļ���{$mkcachefile} �����޷�������Ŀ��");
        if(count($typeidsok)>0)
        {
            fwrite($fp,"<"."?php\r\n");
            $i = -1;
            foreach($typeidsok as $k=>$t)
            {
                if($k!='')
                {
                    $i++;
                    fwrite($fp, "\$idArray[$i]={$k};\r\n");
                }
            }
            fwrite($fp,"?".">");
            fclose($fp);
            ShowMsg("<span style='color:#333;font-family:΢���ź�;font-size:12px; '>�����Ŀ���洦������ת�������Ŀ��</span>","makehtml_list_action.php?gotype=mkall");
            exit();
        }
        else
        {
            fclose($fp);
            ShowMsg("<span style='color:#333;font-family:΢���ź�;font-size:12px; '>û�пɸ��µ���Ŀ����������������Ż���</span>","makehtml_all.php?action=make&step=10");
            exit();
        }
    }

    else if($step==10)
    {
        $adminID = $cuserLogin->getUserID();
        $mkcachefile = DEDEDATA."/mkall_cache_{$adminID}.php";
        @unlink($mkcachefile);
        OptimizeData($dsql);
        ShowMsg("<span style='color:#333;font-family:΢���ź�;font-size:12px; '>��������ļ��ĸ��£�</span>","javascript:;");
        exit();
    }

}

function OptimizeData($dsql)
{
    global $cfg_dbprefix;
    $tptables = array("{$cfg_dbprefix}archives","{$cfg_dbprefix}arctiny");
    $dsql->SetQuery("SELECT maintable,addtable FROM `#@__channeltype` ");
    $dsql->Execute();
    while($row = $dsql->GetObject())
    {
        $addtable = str_replace('#@__',$cfg_dbprefix,$row->addtable);
        if($addtable!='' && !in_array($addtable,$tptables)) $tptables[] = $addtable;
    }
    $tptable = '';
    foreach($tptables as $t) $tptable .= ($tptable=='' ? "`{$t}`" : ",`{$t}`" );
    $dsql->ExecuteNoneQuery(" OPTIMIZE TABLE $tptable; ");
}