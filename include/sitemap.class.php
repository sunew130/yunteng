<?php    if(!defined('DEDEINC')) exit("Request Error!");

require_once(DEDEINC."/channelunit.func.php");

class SiteMap
{
    var $dsql;
    var $artDir;
    var $baseDir;

    function __construct()
    {
        $this->idCounter = 0;
        $this->artDir = $GLOBALS['cfg_arcdir'];
        $this->baseDir = $GLOBALS['cfg_cmspath'].$GLOBALS['cfg_basedir'];
        $this->idArrary = "";
        $this->dsql = $GLOBALS['dsql'];
    }

    function SiteMap()
    {
        $this->__construct();
    }

    function Close()
    {
    }

    function GetSiteMap($maptype="site")
    {
        $mapString = "";
        if($maptype=="rss")
        {
            $this->dsql->SetQuery("SELECT id,typedir,isdefault,defaultname,typename,ispart,namerule2,moresite,siteurl,sitepath FROM #@__arctype WHERE ishidden<>1 AND reid=0 AND ispart<>2 ORDER BY sortrank");
        }
        else
        {
            $this->dsql->SetQuery("SELECT id,typedir,isdefault,defaultname,typename,ispart,namerule2,siteurl,sitepath,moresite,siteurl,sitepath FROM #@__arctype WHERE reid=0 AND ishidden<>1 ORDER BY sortrank");
        }
        $this->dsql->Execute(0);
        while($row=$this->dsql->GetObject(0))
        {
            if($maptype=="site")
            {
                $typelink = GetTypeUrl($row->id,MfTypedir($row->typedir),$row->isdefault,$row->defaultname,$row->ispart,$row->namerule2,$row->moresite,$row->siteurl,$row->sitepath);
            }
            else
            {
                $typelink = $GLOBALS['cfg_cmsurl']."/data/rss/".$row->id.".xml";
            }
            $mapString .= "<div class=\"linkbox\">\r\n<h3><a href='$typelink'>".$row->typename."</a></h3>";
            $mapString .= "\t<ul class=\"f6\">\t\t\r".$this->LogicListAllSunType($row->id,$maptype)."\t\n</ul></div>\r\n";
            /*
            $mapString .= "<tr><td width='17%' align='center' bgcolor='#FAFEF1'>";
            $mapString .= "<a href='$typelink'><b>".$row->typename."</b></a>";
            $mapString .= "</td><td width='83%' bgcolor='#FFFFFF'>";
            $mapString .= $this->LogicListAllSunType($row->id,$maptype);
            $mapString .= "</td></tr>";
            */
        }
        return $mapString;
    }

    function LogicListAllSunType($id, $maptype)
    {
        $fid = $id;
        $mapString = "";
        if($maptype=="rss")
        {
            $this->dsql->SetQuery("SELECT id,typedir,isdefault,defaultname,typename,ispart,namerule2,moresite,siteurl,sitepath FROM #@__arctype WHERE reid='".$id."' AND ishidden<>1 AND ispart<>2 ORDER BY sortrank");
        }
        else
        {
            $this->dsql->SetQuery("SELECT id,typedir,isdefault,defaultname,typename,ispart,namerule2,moresite,siteurl,sitepath FROM #@__arctype WHERE reid='".$id."' AND ishidden<>1 ORDER BY sortrank");
        }
        $this->dsql->Execute($fid);
        while($row=$this->dsql->GetObject($fid))
        {
            if($maptype=="site")
            {
                $typelink = GetTypeUrl($row->id,MfTypedir($row->typedir),$row->isdefault,$row->defaultname,$row->ispart,$row->namerule2,$row->moresite,$row->siteurl,$row->sitepath);
            }
            else
            {
                $typelink = $GLOBALS['cfg_cmsurl']."/data/rss/".$row->id.".xml";
            }
            $mapString .= "<li><a href='$typelink'>".$row->typename."</a></li>\n\t\t";
            $mapString .= $this->LogicListAllSunType($row->id,$maptype);
        }
        return $mapString;
    }
}