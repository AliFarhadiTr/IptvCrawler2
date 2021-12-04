<?php

function GetLastIPTVLinks(){
    $this_file=dirname(__FILE__);
    $myfile = fopen($this_file."/database/last.csv", "r") or die("Unable to open file!");
    $path='/output/'. fread($myfile,filesize($this_file."/database/last.csv"));
    fclose($myfile);

    $myfile = fopen($this_file."/database/last2.csv", "r") or die("Unable to open file!");
    $Github= fread($myfile,filesize($this_file."/database/last2.csv"));
    fclose($myfile);

    $last_link=sprintf(
        "%s://%s%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_NAME'],$_SERVER['SERVER_PORT']==80?'':':'.$_SERVER['SERVER_PORT'],
        $path
    );

    return [
        'status'=>1,
        'link'=>$last_link,
        'Github'=>$Github
    ];
}