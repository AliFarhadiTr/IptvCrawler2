<?php

require_once 'config.php';
$last_link=url('/output/'.file_get_contents(ABS_PATH.'/database/last.csv',true));

header('Content-Type: application/json; charset=utf-8');
echo json_encode(
    [
        'status'=>1,
        'link'=>$last_link,
        'Github'=>file_get_contents(ABS_PATH.'/database/last2.csv',true)
    ]
    ,JSON_PRETTY_PRINT);

