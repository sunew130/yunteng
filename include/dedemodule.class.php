<?php   if(!defined('DEDEINC')) exit("Request Error!");

require_once(DEDEINC.'/charset.func.php');
require_once(DEDEINC.'/dedeatt.class.php');
require_once(DEDEINC.'/dedehttpdown.class.php');

class DedeModule
{
    var $modulesPath;
	var $modulesUrl;
    var $modules;
    var $fileListNames;
    var $sysLang;
    var $moduleLang;
    function __construct($modulespath='',$modulesUrl='')
    {
        global $cfg_soft_lang;
        $this->sysLang = $this->moduleLang = $cfg_soft_lang;
        $this->fileListNames = array();
        $this->modulesPath = $modulespath;
		$this->modulesUrl = $modulesUrl;
    }
    function DedeModule($modulespath='')
    {
        $this->__construct($modulespath);
    }

    function GetModuleList($moduletype='')
    {
        if(is_array($this->modules)) return $this->modules;

        $dh = dir($this->modulesPath) or die("û�ҵ�ģ��Ŀ¼��({$this->modulesPath})��");

        $fp = @fopen($this->modulesPath.'/modulescache.php','w') or die('��ȡ�ļ�Ȩ�޳���,Ŀ¼�ļ�'.$this->modulesPath.'/modulescache.php����д!');

        fwrite($fp, "<"."?php\r\n");
        fwrite($fp, "global \$allmodules;\r\n");
        while($filename = $dh->read())
        {
            if(preg_match("/\.xml$/i", $filename))
            {
                $minfos = $this->GetModuleInfo(str_replace('.xml','',$filename));
                if(isset($minfos['moduletype']) && $moduletype!='' && $moduletype!=$minfos['moduletype'])
                {
                    continue;
                }
                if($minfos['hash']!='')
                {
                    $this->modules[$minfos['hash']] = $minfos;
                    fwrite($fp, '$'."GLOBALS['allmodules']['{$minfos['hash']}']='{$filename}';\r\n");
                }
            }
        }
        fwrite($fp,'?'.'>');
        fclose($fp);
        $dh->Close();
        return $this->modules;
    }

    function GetModuleUrlList($moduletype='',$url='')
    {
		$dh = dir($this->modulesPath) or die("û�ҵ�ģ��Ŀ¼��({$this->modulesPath})��");
        $fp = @fopen($this->modulesPath.'/modulescache.php','w') or die('��ȡ�ļ�Ȩ�޳���,Ŀ¼�ļ�'.$this->modulesPath.'/modulescache.php����д!');
		$cachefile = DEDEDATA.'/module/moduleurllist.txt';
		$remotelist = '';
		if(file_exists($cachefile) && (filemtime($cachefile) + 60 * 30) > time())
		{
			$remotelist = file_get_contents($cachefile);
		} else {
			$del = new DedeHttpDown();
			$del->OpenUrl($url);
			$remotelist = $del->GetHtml();
			PutFile($cachefile, $remotelist);
		}
		if(empty($remotelist)) return false;
		
        $modules = unserialize($remotelist);
		if(empty($moduletype)){
			return $modules;
		}
		$return = array();
		foreach($modules as $arrow=>$data) {
			if($data['moduletype']==$moduletype)
				$return[] =  $data;
		}
		return $return;
    }

    function AppCode(&$str)
    {
        if($this->moduleLang==$this->sysLang)
        {
            return $str;
        }
        else
        {
            if($this->sysLang=='utf-8')
            {
                if($this->moduleLang=='gbk') return gb2utf8($str);
                if($this->moduleLang=='big5') return gb2utf8(big52gb($str));
            }
            else if($this->sysLang=='gbk')
            {
                if($this->moduleLang=='utf-8') return utf82gb($str);
                if($this->moduleLang=='big5') return big52gb($str);
            }
            else if($this->sysLang=='big5')
            {
                if($this->moduleLang=='utf-8') return gb2big5(utf82gb($str));
                if($this->moduleLang=='gbk') return gb2big5($str);
            }
            else
            {
                return $str;
            }
        }
    }

    function GetHashFile($hash)
    {
        include_once($this->modulesPath.'/modulescache.php');
        if(isset($GLOBALS['allmodules'][$hash])) return $GLOBALS['allmodules'][$hash];
        else return $hash.'.xml';
    }

    function GetModuleInfo($hash, $ftype='hash')
    {
        if($ftype=='file') $filename = $hash;
		else if(!empty($this->modulesUrl)) {
			$filename = $this->modulesUrl.$hash.'.xml';
		}else $filename = $this->modulesPath.'/'.$this->GetHashFile($hash);
        $start = 0;
        $minfos = array();
        $minfos['name']=$minfos['team']=$minfos['time']=$minfos['email']=$minfos['url']='';
        $minfos['hash']=$minfos['indexname']=$minfos['indexurl']='';
        $minfos['ismember']=$minfos['autosetup']=$minfos['autodel']=0;
        //$minfos['filename'] = $filename;
		if(empty($this->modulesUrl)){
			$minfos['filesize'] = filesize($filename)/1024;
			$minfos['filesize'] = number_format($minfos['filesize'],2,'.','').' Kb';
		}
        $fp = fopen($filename,'r') or die("�ļ� {$filename} �����ڻ򲻿ɶ�!");
        $n = 0;
        while(!feof($fp))
        {
            $n++;
            if($n > 30) break;
            $line = fgets($fp,256);
            if($start==0)
            {  if(preg_match("/<baseinfo/is",$line)) $start = 1; }
            else
            {
                if(preg_match("/<\/baseinfo/is",$line)) break;
                $line = trim($line);
                list($skey,$svalue) = explode('=',$line);
                $skey = trim($skey);
                $minfos[$skey] = $svalue;
            }
        }
        fclose($fp);

        if(isset($minfos['lang'])) $this->moduleLang = trim($minfos['lang']);
        else $this->moduleLang = 'gbk';

        if($this->sysLang=='gb2312') $this->sysLang = 'gbk';
        if($this->moduleLang=='gb2312') $this->moduleLang = 'gbk';

        if($this->sysLang != $this->moduleLang)
        {
            foreach($minfos as $k=>$v) $minfos[$k] = $this->AppCode($v);
        }

        return $minfos;
    }

    function GetFileXml($hash, $ftype='hash')
    {
        if($ftype=='file') $filename = $hash;
        else $filename = $this->modulesPath.'/'.$this->GetHashFile($hash);
        $filexml = '';
        $fp = fopen($filename,'r') or die("�ļ� {$filename} �����ڻ򲻿ɶ�!");
        $start = 0;
        while(!feof($fp))
        {
            $line = fgets($fp,1024);
            if($start==0)
            {
                if(preg_match("/<modulefiles/is",$line))
                {
                    $filexml .= $line;
                    $start = 1;
                }
                continue;
            }
            else
            {
                $filexml .= $line;
            }
        }
        fclose($fp);
        return $filexml;
    }

    function GetSystemFile($hashcode, $ntype, $enCode=TRUE)
    {
        $this->GetModuleInfo($hashcode,$ntype);
        $start = FALSE;
        $filename = $this->modulesPath.'/'.$this->GetHashFile($hashcode);
        $fp = fopen($filename,'r') or die("�ļ� {$filename} �����ڻ򲻿ɶ�!");
        $okdata = '';
        while(!feof($fp))
        {
            $line = fgets($fp,1024);
            if(!$start)
            {
                if(preg_match("#<{$ntype}>#i", $line)) $start = TRUE;
            }
            else
            {
                if(preg_match("#<\/{$ntype}#i", $line)) break;
                $okdata .= $line;
                unset($line);
            }
        }
        fclose($fp);
        $okdata = trim($okdata);
        if(!empty($okdata) && $enCode) $okdata = base64_decode($okdata);
        $okdata = $this->AppCode($okdata);
        return $okdata;
    }

    function WriteSystemFile($hashcode, $ntype)
    {
        $filename = $hashcode."-{$ntype}.php";
        $fname = $this->modulesPath.'/'.$filename;
        $filect = $this->GetSystemFile($hashcode,$ntype);
        $fp = fopen($fname,'w') or die('���� {$ntype} �ļ�ʧ�ܣ�');
        fwrite($fp,$filect);
        fclose($fp);
        return $filename;
    }

    function DelSystemFile($hashcode,$ntype)
    {
        $filename = $this->modulesPath.'/'.$hashcode."-{$ntype}.php";
        unlink($filename);
    }

    function HasModule($hashcode)
    {
        $modulefile = $this->modulesPath.'/'.$this->GetHashFile($hashcode);
        if(file_exists($modulefile) && !is_dir($modulefile)) return TRUE;
        else  return FALSE;
    }

    function GetEncodeFile($filename,$isremove=FALSE)
    {
        $fp = fopen($filename,'r') or die("�ļ� {$filename} �����ڻ򲻿ɶ�!");
        $str = @fread($fp,filesize($filename));
        fclose($fp);
        if($isremove) @unlink($filename);
        if(!empty($str)) return base64_encode($str);
        else return '';
    }

    function GetFileLists($hashcode)
    {
        $dap = new DedeAttParse();
        $filelists = array();
        $modulefile = $this->modulesPath.'/'.$this->GetHashFile($hashcode);
        $fp = fopen($modulefile,'r') or die("�ļ� {$modulefile} �����ڻ򲻿ɶ�!");
        $i = 0;
        while(!feof($fp))
        {
            $line = fgets($fp,1024);
            if(preg_match("/^[\s]{0,}<file/i",$line))
            {
                $i++;
                $line = trim(preg_replace("/[><]/","",$line));
                $dap->SetSource($line);
                $filelists[$i]['type'] = $dap->CAtt->GetAtt('type');
                $filelists[$i]['name'] = $dap->CAtt->GetAtt('name');
            }
        }
        fclose($fp);
        return $filelists;
    }

    function DeleteFiles($hashcode,$isreplace=0)
    {
        if($isreplace==0) return TRUE;
        else
        {
            $dap = new DedeAttParse();
            $modulefile = $this->modulesPath.'/'.$this->GetHashFile($hashcode);
            $fp = fopen($modulefile,'r') or die("�ļ� {$modulefile} �����ڻ򲻿ɶ�!");
            $i = 0;
            $dirs = '';
            while(!feof($fp))
            {
                $line = fgets($fp,1024);
                if(preg_match("/^[\s]{0,}<file/i",$line))
                {
                    $i++;
                    $line = trim(preg_replace("/[><]/","",$line));
                    $dap->SetSource($line);
                    $filetype = $dap->CAtt->GetAtt('type');
                    $filename = $dap->CAtt->GetAtt('name');
                    $filename = str_replace("\\","/",$filename);
                    if($filetype=='dir'){ $dirs[] = $filename; }
                    else{ @unlink($filename); }
                }
            }
            $okdirs = array();
            if(is_array($dirs)){
                $st = count($dirs) -1;
                for($i=$st;$i>=0;$i--){  @rmdir($dirs[$i]); }
            }
            fclose($fp);
        }
        return TRUE;
    }

    function WriteFiles($hashcode, $isreplace=3)
    {
        global $AdminBaseDir;
        $dap = new DedeAttParse();
        $modulefile = $this->modulesPath.'/'.$this->GetHashFile($hashcode);
        $fp = fopen($modulefile,'r') or die("�ļ� {$modulefile} �����ڻ򲻿ɶ�!");
        $i = 0;
        while(!feof($fp))
        {
            $line = fgets($fp,1024);
            if( preg_match("/^[\s]{0,}<file/i",$line) )
            {
                $i++;
                $line = trim(preg_replace("/[><]/","",$line));
                $dap->SetSource($line);
                $filetype = $dap->CAtt->GetAtt('type');
                $filename = $dap->CAtt->GetAtt('name');
                $filename = str_replace("\\","/",$filename);
                if(!empty($AdminBaseDir)) $filename = $AdminBaseDir.$filename;
                if($filetype=='dir')
                {
                    if(!is_dir($filename))
                    {
                        @mkdir($filename,$GLOBALS['cfg_dir_purview']);
                    }
                    @chmod($filename,$GLOBALS['cfg_dir_purview']);
                }
                else
                {
                    $this->TestDir($filename);
                    if($isreplace==0) continue;
                    if($isreplace==3)
                    {
                        if(is_file($filename))
                        {
                            $copyname = @preg_replace("/([^\/]{1,}$)/","bak-$1",$filename);
                            @copy($filename,$copyname);
                        }
                    }
                    if(!empty($filename))
                    {
                        $fw = fopen($filename,'w') or die("д���ļ� {$filename} ʧ�ܣ��������Ŀ¼��Ȩ�ޣ�");
                        $ct = '';
                        while(!feof($fp))
                        {
                            $l = fgets($fp,1024);
                            if(preg_match("/^[\s]{0,}<\/file/i",trim($l))){ break; }
                            $ct .= $l;
                        }
                        $ct = base64_decode($ct);
                        if($this->sysLang!=$this->moduleLang)
                        {

                            if(preg_match('/\.(xml|php|inc|txt|htm|html|shtml|tpl|css)$/', $filename))
                            {
                                $ct = $this->AppCode($ct);
                            }

                            if(preg_match('/\.(php|htm|html|shtml|inc|tpl)$/i', $filename))
                            {
                                if($this->sysLang=='big5') $charset = 'charset=big5';
                                else if($this->sysLang=='utf-8') $charset = 'charset=gb2312';
                                else  $charset = 'charset=gb2312';
                                $ct = preg_match("/charset=([a-z0-9-]*)/i", $charset, $ct);
                            }
                        }
                        fwrite($fw,$ct);
                        fclose($fw);
                    }
                }
            }
        }
        fclose($fp);
        return TRUE;
    }

    function TestDir($filename)
    {
        $fs = explode('/',$filename);
        $fn = count($fs) - 1 ;
        $ndir = '';
        for($i=0;$i < $fn;$i++)
        {
            if($ndir!='') $ndir = $ndir.'/'.$fs[$i];
            else $ndir = $fs[$i];
            $rs = @is_dir($ndir);
            if( !$rs ) {
                @mkdir($ndir,$GLOBALS['cfg_dir_purview']);
                @chmod($ndir,$GLOBALS['cfg_dir_purview']);
            }
        }
        return TRUE;
    }

    function MakeEncodeFile($basedir,$f,$fp)
    {
        $this->fileListNames = array();
        $this->MakeEncodeFileRun($basedir,$f,$fp);
        return TRUE;
    }

    function MakeEncodeFileTest($basedir,$f)
    {
        $this->fileListNames = array();
        $this->MakeEncodeFileRunTest($basedir,$f);
        return TRUE;
    }

    function MakeEncodeFileRunTest($basedir,$f)
    {
        $filename = $basedir.'/'.$f;
        if(isset($this->fileListNames[$f])) return;
        else if(preg_match("/Thumbs\.db/i",$f)) return;
        else $this->fileListNames[$f] = 1;
        $fileList = '';
        if(!file_exists($filename))
        {
            ShowMsg("�ļ����ļ���: {$filename} �����ڣ��޷����б���!","-1");
            exit();
        }
        if(is_dir($filename))
        {
            $dh = dir($filename);
            while($filename = $dh->read())
            {
                if($filename[0]=='.' || strtolower($filename)=='cvs') continue;
                $nfilename = $f.'/'.$filename;
                $this->MakeEncodeFileRunTest($basedir,$nfilename);
            }
        }
    }

    function MakeEncodeFileRun($basedir,$f,$fp)
    {
        $filename = $basedir.'/'.$f;
        if(isset($this->fileListNames[$f])) return;
        else if(preg_match("#Thumbs\.db#i", $f)) return;
        else $this->fileListNames[$f] = 1;
        $fileList = '';
        if(is_dir($filename))
        {
            $fileList .= "<file type='dir' name='$f'>\r\n";
            $fileList .= "</file>\r\n";
            fwrite($fp,$fileList);
            $dh = dir($filename);
            while($filename = $dh->read())
            {
                if($filename[0]=='.' || strtolower($filename)=='cvs') continue;
                $nfilename = $f.'/'.$filename;
                $this->MakeEncodeFileRun($basedir,$nfilename,$fp);
            }
        }
        else
        {
            $fileList .= "<file type='file' name='$f'>\r\n";
            $fileList .= $this->GetEncodeFile($filename);
            $fileList .= "\r\n</file>\r\n";
            fwrite($fp,$fileList);
        }
    }

    function Clear()
    {
        unset($this->modules);
        unset($this->fileList);
        unset($this->fileListNames);
    }

}