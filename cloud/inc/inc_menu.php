<?php
require_once(dirname(__FILE__)."/../config.php");

$addset = '';

if($cfg_admin_channel = 'array' && count($admin_catalogs) > 0)
{
    $admin_catalog = join(',', $admin_catalogs);
    $dsql->SetQuery(" SELECT channeltype FROM `#@__arctype` WHERE id IN({$admin_catalog}) GROUP BY channeltype ");
}
else
{
    $dsql->SetQuery(" SELECT channeltype FROM `#@__arctype` GROUP BY channeltype ");
}
$dsql->Execute();
$candoChannel = '';
while($row = $dsql->GetObject())
{
    $candoChannel .= ($candoChannel=='' ? $row->channeltype : ','.$row->channeltype);
}
if(empty($candoChannel)) $candoChannel = 1;
$dsql->SetQuery("SELECT id,typename,addcon,mancon FROM `#@__channeltype` WHERE id IN({$candoChannel}) AND id<>-1 AND isshow=1 ORDER BY id ASC");
$dsql->Execute();
while($row = $dsql->GetObject())
{
    $addset .= "";
}

$adminMenu1 = $adminMenu2 = '';
if($cuserLogin->getUserType() >= 10)
{
$adminMenu1 = "";
$adminMenu2 = "
<m:top item='7_' name='�ɼ�����' display='none' rank='co_NewRule,co_ListNote,co_ViewNote,co_Switch,co_GetOut'>
  <m:item name='�ɼ��ڵ����' link='co_main.php' rank='co_ListNote' target='main' />
  <m:item name='��ʱ���ݹ���' link='co_url.php' rank='co_ViewNote' target='main' />
  <m:item name='����ɼ�����' link='co_get_corule.php' rank='co_GetOut' target='main'/>
  <m:item name='��زɼ�ģʽ' link='co_gather_start.php' rank='co_GetOut' target='main'/>
  <m:item name='�ɼ�δ��������' link='co_do.php?dopost=coall' rank='co_GetOut' target='main'/>
</m:top>
<m:top item='7_' name='ģ�����' display='none' rank='temp_One,temp_Other,temp_MyTag,temp_test,temp_All'>
  <m:item name='Ĭ��ģ�����' link='templets_main.php' rank='temp_All' target='main'/>
</m:top>
<m:top item='7_' name='��������' display='none' rank='sys_Upload,sys_MyUpload,plus_�ļ�������'>
  <m:item name='ͼƬ����' link='media_main.php?dopost=filemanager' rank='plus_�ļ�������' target='main' />
  <m:item name='�ϴ����ļ�' link='media_add.php' rank='' target='main' />
  <m:item name='�������ݹ���' link='media_main.php' rank='sys_Upload,sys_MyUpload' target='main' />
</m:top>
<m:top item='10_' name='ϵͳ����' display='none' rank='sys_User,sys_Group,sys_Edit,sys_Log,sys_Data'>
  <m:item name='ϵͳ��������' link='sys_info.php' rank='sys_Edit' target='main' />
  <m:item name='�ļ�ʽ������' link='file_manage_main.php' rank='sys_Edit' target='main' />
  <m:item name='�������ӹ���' link='friendlink_main.php' rank='sys_Edit' target='main' />
  <m:item name='ͼƬˮӡ����' link='sys_info_mark.php' rank='sys_Edit' target='main' />
  <m:item name='�Զ����' link='diy_main.php' rank='c_List' target='main' />
</m:top>
<m:top item='10_' name='���ݻָ�' display='none' rank='sys_User,sys_Group,sys_Edit,sys_Log,sys_Data'>
  <m:item name='���ݿⱸ��' link='sys_data.php' rank='sys_Data' target='main' />
  <m:item name='���ݿ�ָ�' link='sys_data_revert.php' rank='sys_Data' target='main' />
</m:top>
<m:top item='10_' name='��������' display='none' rank='sys_User,sys_Group,sys_Edit,sys_Log,sys_Data'>
  <m:item name='ϵͳ�û�����' link='sys_admin_user.php' rank='sys_User' target='main' />
  <m:item name='�û������趨' link='sys_group.php' rank='sys_Group' target='main' />
  <m:item name='����ģ�͹���' link='mychannel_main.php' rank='c_List' target='main' />
  <m:item name='ϵͳ��־����' link='log_list.php' rank='sys_Log' target='main' />
</m:top>
";
}
$remoteMenu = ($cfg_remote_site=='Y')? "" : "";
$menusMain = "
-----------------------------------------------

<m:top item='1_' name='��Ŀ����' display='block'>
  <m:item name='��վ��Ŀ' link='catalog_main.php' ischannel='1' addalt='������Ŀ' linkadd='catalog_add.php?listtype=all' rank='t_List,t_AccList' target='main' />
  <m:item name='�ظ����' link='article_test_same.php' rank='sys_ArcBatch' target='main' />
  <m:item name='�Զ�ժҪ' link='article_description_main.php' rank='sys_Keyword' target='main' />
  <m:item name='����վ' link='recycling.php' ischannel='1' addalt='��ջ���վ' addico='images/gtk-del.gif' linkadd='archives_do.php?dopost=clear&aid=no&recycle=1' rank='a_List,a_AccList,a_MyList' target='main' />
</m:top>
<m:top item='1_' name='��������' display='none' rank='temp_One,temp_Other,temp_MyTag,temp_test,temp_All'>
  <m:item name='����ϵͳ����' link='sys_cache_up.php' rank='sys_ArcBatch' target='main' />
  <m:item name='������ҳHTML' link='makehtml_homepage.php' rank='sys_MakeHtml' target='main' />
  <m:item name='������ĿHTML' link='makehtml_list.php' rank='sys_MakeHtml' target='main' />
  <m:item name='�����ĵ�HTML' link='makehtml_archives.php' rank='sys_MakeHtml' target='main' />
</m:top>

$adminMenu1

$adminMenu2

<m:top item='1_7_10_' name='��������' display='none'>
  <m:item name='��վ����' link='http://www.yunteng.cc' rank='' target='_blank' />
  <m:item name='��������' link='http://idc.yunteng.cc' rank='' target='_blank' />
  <m:item name='��������' link='http://www.reg.wang' rank='' target='_blank' />
  <m:item name='��Ѷ����̳' link='http://bbs.qcloud.com' rank='' target='_blank' />
  <m:item name='��ϵ����' link='http://wpa.qq.com/msgrd?v=3&uin=503827438&site=qq&menu=yes' rank='' target='_blank' />
</m:top>

-----------------------------------------------
";