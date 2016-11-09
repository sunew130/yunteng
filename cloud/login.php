<?php

require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(DEDEINC.'/userlogin.class.php');
if(empty($dopost)) $dopost = '';

if( is_dir(dirname(__FILE__).'/../_install') )
{
    if(!file_exists(dirname(__FILE__).'/../install/yt_install.txt') )
    {
      $fp = fopen(dirname(__FILE__).'/../install/yt_install.txt', 'w') or die('��װĿ¼��д��Ȩ�ޣ��޷�����д�������ļ����밲װ���ɾ����װĿ¼��');
      fwrite($fp,'ok');
      fclose($fp);
    }

    if( file_exists("../install/index.php") ) {
        @rename("../install/index.php", "../install/index.php.bak");
    }
    if( file_exists("../install/module-install.php") ) {
        @rename("../install/module-install.php", "../install/module-install.php.bak");
    }
	$fileindex = "../install/index.html";
	if( !file_exists($fileindex) ) {
		$fp = @fopen($fileindex,'w');
		fwrite($fp,'dir');
		fclose($fp);
	}
}

$cururl = GetCurUrl();
if(preg_match('/admin\/login/i',$cururl))
{
    $redmsg = '<div class=\'safe-tips\'>���Ĺ���Ŀ¼�������а���Ĭ������admin��������FTP������޸�Ϊ�������ƣ����������ȫ��</div>';
}
else
{
    $redmsg = '';
}

$admindirs = explode('/',str_replace("\\",'/',dirname(__FILE__)));
$admindir = $admindirs[count($admindirs)-1];
if($dopost=='login')
{
    $validate = empty($validate) ? '' : strtolower(trim($validate));
    $svali = strtolower(GetCkVdValue());
    if(($validate=='' || $validate != $svali) && preg_match("/6/",$safe_gdopen)){
        ResetVdValue();
        ShowMsg('��֤�벻��ȷ!','login.php',0,1000);
        exit;
    } else {
        $cuserLogin = new userLogin($admindir);
        if(!empty($userid) && !empty($pwd))
        {
            $res = $cuserLogin->checkUser($userid,$pwd);


            if($res==1)
            {
                $cuserLogin->keepUser();
                if(!empty($gotopage))
                {
                    ShowMsg('�ɹ���¼������ת����������ҳ��',"../cloud/index.php");
                    exit();
                }
                else
                {
                    ShowMsg('�ɹ���¼������ת����������ҳ��',"../cloud/index.php");
                    exit();
                }
            }

            else if($res==-1)
            {
				ShowMsg('����û���������!',-1,0,1000);
				exit;
            }
            else
            {
                ShowMsg('����������!',-1,0,1000);
				exit;
            }
        }

        else
        {
            ShowMsg('�û�������û��д����!',-1,0,1000);
			exit;
        }
    }
}

include('templets/login.htm');