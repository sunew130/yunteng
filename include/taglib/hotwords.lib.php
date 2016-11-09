<?php

function lib_hotwords(&$ctag,&$refObj)
{
    global $cfg_phpurl,$dsql;

    $attlist="num|6,subday|365,maxlength|16";
    FillAttsDefault($ctag->CAttribute->Items,$attlist);
    extract($ctag->CAttribute->Items, EXTR_SKIP);

    $nowtime = time();
    if(empty($subday)) $subday = 365;
    if(empty($num)) $num = 6;
    if(empty($maxlength)) $maxlength = 20;
    $maxlength = $maxlength+1;
    $mintime = $nowtime - ($subday * 24 * 3600);
    $dsql->SetQuery("SELECT keyword FROM `#@__search_keywords` WHERE lasttime>$mintime AND length(keyword)<$maxlength ORDER BY count DESC LIMIT 0,$num");
    $dsql->Execute('hw');
    $hotword = '';
    while($row=$dsql->GetArray('hw')){
        $hotword .= "��<a href='".$cfg_phpurl."/search.php?keyword=".urlencode($row['keyword'])."'>".$row['keyword']."</a> ";
    }
    return $hotword;
}