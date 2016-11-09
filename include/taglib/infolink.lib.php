<?php   if(!defined('DEDEINC')) exit('Request Error!');

require_once(DEDEINC.'/enums.func.php');
require_once(DEDEDATA.'/enums/nativeplace.php');
require_once(DEDEDATA.'/enums/infotype.php');

function lib_infolink(&$ctag,&$refObj)
{
    global $dsql,$nativeplace,$infotype,$hasSetEnumJs,$cfg_cmspath,$cfg_mainsite;
    global $em_nativeplaces,$em_infotypes;

    $cmspath = ( (empty($cfg_cmspath) || !preg_match("#\/$#", $cfg_cmspath)) ? $cfg_cmspath.'/' : $cfg_cmspath );
    $baseurl = preg_replace("#\/$#", '', $cfg_mainsite).$cmspath;
    
    $smalltypes = '';
    if( !empty($refObj->TypeLink->TypeInfos['smalltypes']) ) {
        $smalltypes = explode(',', $refObj->TypeLink->TypeInfos['smalltypes']);
    }
    
    if(empty($refObj->Fields['typeid'])) {
        $row = $dsql->GetOne("SELECT id FROM `#@__arctype` WHERE channeltype='-8' And reid = '0' ");
        $typeid = (is_array($row) ? $row['id'] : 0);
    }
    else {
        $typeid = $refObj->Fields['typeid'];
    }
    
    $innerText = trim($ctag->GetInnerText());
    if(empty($innerText)) $innerText = GetSysTemplets("info_link.htm");
    $ctp = new DedeTagParse();
    $ctp->SetNameSpace('field','[',']');
    $ctp->LoadSource($innerText);

    $revalue = $seli = '';
    $channelid = ( empty($refObj->TypeLink->TypeInfos['channeltype']) ? -8 : $refObj->TypeLink->TypeInfos['channeltype'] );
    
    $fields = array('nativeplace'=>'','infotype'=>'','typeid'=>$typeid,
                    'channelid'=>$channelid,'linkallplace'=>'','linkalltype'=>'');
    
    $fields['nativeplace'] = $fields['infotype'] = '';
    
    $fields['linkallplace'] = "<a href='{$baseurl}yunteng_cc_plus/list.php?channelid={$channelid}&tid={$typeid}&infotype={$infotype}'>����</a>";
    $fields['linkalltype'] = "<a href='{$baseurl}yunteng_cc_plus/list.php?channelid={$channelid}&tid={$typeid}&nativeplace={$nativeplace}'>����</a>";

    if(empty($nativeplace))
    {
        foreach($em_nativeplaces as $eid=>$em)
        {
            if($eid % 500 != 0) continue;
            $fields['nativeplace'] .= " <a href='{$baseurl}yunteng_cc_plus/list.php?channelid={$channelid}&tid={$typeid}&nativeplace={$eid}&infotype={$infotype}'>{$em}</a>\r\n";
        }
    }
    else
    {
        $sontype = ( ($nativeplace % 500 != 0) ? $nativeplace : 0 );
        $toptype = ( ($nativeplace % 500 == 0) ? $nativeplace : ( $nativeplace-($nativeplace%500) ) );
        $fields['nativeplace'] = "<a href='{$baseurl}yunteng_cc_plus/list.php?channelid={$channelid}&tid={$typeid}&nativeplace={$toptype}&infotype={$infotype}'> <b>{$em_nativeplaces[$toptype]}</b></a> &gt;&gt; ";
        foreach($em_nativeplaces as $eid=>$em)
        {
            if($eid < $toptype+1 || $eid > $toptype+499) continue;
            if($eid == $nativeplace) {
                $fields['nativeplace'] .= " <b>{$em}</b>\r\n";
            }
            else {
                $fields['nativeplace'] .= " <a href='{$baseurl}yunteng_cc_plus/list.php?channelid={$channelid}&tid={$typeid}&nativeplace={$eid}&infotype={$infotype}'>{$em}</a>\r\n";
          }
      }
    }

    if(empty($infotype) || is_array($smalltypes))
    {
        
        foreach($em_infotypes as $eid=>$em)
        {
            if(!is_array($smalltypes) && $eid % 500 != 0) continue;
            if(is_array($smalltypes) && !in_array($eid, $smalltypes)) continue;
            if($eid == $infotype) 
            {
                $fields['infotype'] .= " <b>{$em}</b>\r\n";
            }
            else {
                $fields['infotype'] .= " <a href='{$baseurl}yunteng_cc_plus/list.php?channelid={$channelid}&tid={$typeid}&infotype={$eid}&nativeplace={$nativeplace}'>{$em}</a>\r\n";
            }
        }
    }
    else
    {
        $sontype = ( ($infotype % 500 != 0) ? $infotype : 0 );
        $toptype = ( ($infotype % 500 == 0) ? $infotype : ( $infotype-($infotype%500) ) );
        $fields['infotype'] .= "<a href='{$baseurl}yunteng_cc_plus/list.php?channelid={$channelid}&tid={$typeid}&infotype={$toptype}&nativeplace={$nativeplace}'><b>{$em_infotypes[$toptype]}</b></a> &gt;&gt; ";
        foreach($em_infotypes as $eid=>$em)
        {
            if($eid < $toptype+1 || $eid > $toptype+499) continue;
            if($eid == $infotype) {
                $fields['infotype'] .= " <b>{$em}</b>\r\n";
            }
            else {
                $fields['infotype'] .= " <a href='{$baseurl}yunteng_cc_plus/list.php?channelid={$channelid}&tid={$typeid}&infotype={$eid}&nativeplace={$nativeplace}'>{$em}</a>\r\n";
          }
      }
    }

    if(is_array($ctp->CTags))
    {
        foreach($ctp->CTags as $tagid=>$ctag)
        {
            if(isset($fields[$ctag->GetName()])) {
                $ctp->Assign($tagid,$fields[$ctag->GetName()]);
            }
        }
        $revalue .= $ctp->GetResult();
    }
    
    return $revalue;
}