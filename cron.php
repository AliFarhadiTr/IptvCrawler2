<?php

require_once 'config.php';
require_once ABS_PATH.'/libs/IPTV.php';
set_time_limit(3600);

$iptv=new IPTV();

$file = $iptv->Fetch();
$git='';
if (GIT_TOKEN!=''){

    $git=Upload2Git(basename($file));
    file_put_contents(ABS_PATH.'/database/last2.csv',$git,LOCK_EX);

}

$last_link=url('/output/'.basename($file));

file_put_contents(ABS_PATH.'/database/last.csv',basename($file),LOCK_EX);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(
    [
        'status'=>1,
        'link'=>$last_link,
        'Github'=>$git
    ]
    ,JSON_PRETTY_PRINT);
