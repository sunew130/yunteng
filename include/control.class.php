<?php   if(!defined('DEDEINC')) exit("Request Error!");

require_once(DEDEINC."/dedetemplate.class.php");

class Control
{
    var $qwl;
    var $tpl;
    var $dsql;
    var $style='default';
    var $_helpers = array();
    
    var $apptpl = '../templates/';
    
    function __construct()
    {
        $this->Control();
    }

    function Control()
    {
        global $dsql;
        $this->tpl = isset($this->tpl)? $this->tpl : new DedeTemplate();
        $sqltype = "DedeSql";
        if ($GLOBALS['cfg_mysql_type'] == 'mysqli' && function_exists("mysqli_init")) $sqltype = "DedeSql";
        else $sqltype = "DedeSqli";
        $this->dsql = isset($dsql)? $dsql : new $sqltype(FALSE);
    }

    function SetTemplate($tplfile)
    {
        $tplfile = DEDEAPPTPL.'/'.$this->style.'/'.$tplfile;
        $this->tpl->LoadTemplate($tplfile);
    }
    function SetTemplet($tplfile)
    {
        $tplfile = DEDEAPPTPL.'/'.$this->style.'/'.$tplfile;
        $this->tpl->LoadTemplate($tplfile);
    }

    function SetVar($k, $v)
    {
        $this->tpl->Assign($k, $v);
    }

    function GetVar($k)
    {
        global $_vars;
        return isset($_vars[$k]) ? $_vars[$k] : '';
    }
    
    function Model($name='')
    {
        $name = preg_replace("#[^\w]#", "", $name);
        $modelfile = DEDEMODEL.'/'.$name.'.php';
        if (file_exists($modelfile))
        {
            require_once $modelfile;
        }
        if (!empty($name) && class_exists($name))
        {
            return new $name;
        } 
        return false;
    }
    
    function Libraries($name='',$data = '')
    {
	if(defined('APPNAME')) 
	{
		$classfile = 'MY_'.$name.'.class.php';
		if ( file_exists ( '../'.APPNAME.'/libraries/'.$classfile ) )
		{
			require '../'.APPNAME.'/libraries/'.$classfile;
			return new $name($data);
		}else{
			if (!empty($name) && class_exists($name))
		        {
		            return new $name($data);
		        }
		}
		return FALSE;
	}else{
		if (!empty($name) && class_exists($name))
	        {
	            return new $name($data);
	        }
	        return FALSE;
	 }
    }  

    function helper($helper = "",$path)
    {   
        $help_path = $path.'/data/helper/'.$helper.".helper.php";
        if (file_exists($help_path))
        { 
            include_once($help_path);
        }else{
            exit('Unable to load the requested file: '.$helper.".helper.php");          
        }  
    }

    function Display()
    {
        $this->tpl->SetObject($this);
        $this->tpl->Display();
    }

    function SaveTo($filename)
    {
        $this->tpl->SetObject($this);
        $this->tpl->SaveTo($filename);
    }

    function __destruct() {
        unset($this->tpl);
        $this->dsql->Close(TRUE);
    }
}