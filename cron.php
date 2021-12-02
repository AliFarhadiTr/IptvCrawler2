<?php
require_once 'config.php';
require_once ABS_PATH.'/libs/IPTV.php';
set_time_limit(3600);

$iptv=new IPTV();

print_r($iptv->Fetch());