<?php
if(!defined('DEDEINC'))
{
    exit("Request Error!");
}
require_once(dirname(__file__).'/../json.class.php');

function lib_json(&$ctag,&$refObj)
{
    global $dsql,$sqlCt,$cfg_soft_lang;
    $attlist="url|";
    FillAttsDefault($ctag->CAttribute->Items,$attlist);
    extract($ctag->CAttribute->Items, EXTR_SKIP);

    $Innertext = trim($ctag->GetInnerText());

    if($url=='' || $Innertext=='') return '';

    $ctp = new DedeTagParse();
    $ctp->SetNameSpace('field','[',']');
    $ctp->LoadSource($Innertext);
    
    $mcache = new MiniCache;

    $GLOBALS['autoindex'] = 0;
    $chash = md5($url);
    
    if(!$row = $mcache->Get($chash))
    {
        $content = @file_get_contents($url);
        
        $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        $row = $json->decode($content);
        
        if($cfg_soft_lang != 'utf-8')
        {
            $row = AutoCharset($row, 'utf-8', 'gb2312');
        }
        $mcache->Save($chash, $row, $cache);
    }

    $revalue = "";

    foreach($row as $key => $value)
    {
        $GLOBALS['autoindex']++;
        foreach($ctp->CTags as $tagid=>$ctag)
        {
            if($ctag->GetName()=='array')
            {
                $ctp->Assign($tagid,$value);
            }
            else
            {
                if( !empty($value[$ctag->GetName()])) 
                { 
                    $ctp->Assign($tagid,$value[$ctag->GetName()]); 
                } else { 
                  $ctp->Assign($tagid,""); 
                }
            }
        }
        $revalue .= $ctp->GetResult();
    }
    
    return $revalue;
}

class MiniCache
{
    var $_cache_path;
    
    function __construct()
    {
        $this->_cache_path = DEDEDATA.'/cache/json/';
    }

	function Get($id)
	{
		if ( ! file_exists($this->_cache_path.$id))
		{
			return FALSE;
		}
		
		$data = $this->_ReadFile($this->_cache_path.$id);
		$data = unserialize($data);
		
		if (time() >  $data['time'] + $data['ttl'])
		{
			unlink($this->_cache_path.$id);
			return FALSE;
		}
		
		return $data['data'];
	}

    function Clean()
	{
		return $this->_DeleteFiles($this->_cache_path);
	}

	function Save($id, $data, $ttl = 60)
	{		
		$contents = array(
				'time'		=> time(),
				'ttl'		=> $ttl,			
				'data'		=> $data
			);
		
		if (PutFile($this->_cache_path.$id, serialize($contents)))
		{
			@chmod($this->_cache_path.$id, 0777);
			return TRUE;			
		}

		return FALSE;
	}

    function Delete($id)
	{
		return unlink($this->_cache_path.$id);
	}
    

	function _DeleteFiles($path, $del_dir = FALSE, $level = 0)
	{

		$path = rtrim($path, DIRECTORY_SEPARATOR);

		if ( ! $current_dir = @opendir($path))
		{
			return FALSE;
		}

		while(FALSE !== ($filename = @readdir($current_dir)))
		{
			if ($filename != "." and $filename != "..")
			{
				if (is_dir($path.DIRECTORY_SEPARATOR.$filename))
				{

					if (substr($filename, 0, 1) != '.')
					{
						delete_files($path.DIRECTORY_SEPARATOR.$filename, $del_dir, $level + 1);
					}
				}
				else
				{
					unlink($path.DIRECTORY_SEPARATOR.$filename);
				}
			}
		}
		@closedir($current_dir);

		if ($del_dir == TRUE AND $level > 0)
		{
			return @rmdir($path);
		}

		return TRUE;
	}

	function _ReadFile($file)
	{
		if ( ! file_exists($file)) return FALSE;
		if (function_exists('file_get_contents')) return file_get_contents($file);
		if ( ! $fp = @fopen($file, FOPEN_READ)) return FALSE;

		flock($fp, LOCK_SH);

		$data = '';
		if (filesize($file) > 0) $data =& fread($fp, filesize($file));
		
		flock($fp, LOCK_UN);
		fclose($fp);

		return $data;
	}
}