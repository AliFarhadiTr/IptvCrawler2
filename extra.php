<?php
require_once 'vendor/autoload.php';

use Github\Client;

function url($path){
    return sprintf(
        "%s://%s%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_NAME'],$_SERVER['SERVER_PORT']==80?'':':'.$_SERVER['SERVER_PORT'],
        $path
    );
}

function Upload2Git($file){
    $year =date("Y");
    $month = date("M");

    $client = Client::createWithHttpClient(new GuzzleHttp\Client([
        'verify' => false
    ]));
    $client->authenticate(GIT_TOKEN,Client::AUTH_ACCESS_TOKEN);

    $reps=$client->currentUser()->repositories();
    $user=$client->currentUser()->show();

    $rep_found=false;
    foreach ($reps as $rep){
        if ($rep['name'] == $year)
        {
            $rep_found = true;
            break;
        }
    }

    if (!$rep_found){
        $client->repositories()->create($year);
    }

    $file_data=file_get_contents(ABS_PATH.'/output/'.$file,true);

    if($client->repositories()->contents()->exists($user['login'],$year,$month."/{$file}")){

        $find_file=$client->repositories()->contents()->show($user['login'],$year,$month."/{$file}");
        $res=$client->repositories()->contents()->update($user['login'],$year,$month."/{$file}",$file_data,
            'updated by script at:'.date('Y/m/d H:i:s'),$find_file['sha']);

    }else{
        $res=$client->repositories()->contents()->create($user['login'],$year,$month."/{$file}",$file_data,
            'created by script at:'.date('Y/m/d H:i:s'));
    }

    return $res['content']['download_url'];
}