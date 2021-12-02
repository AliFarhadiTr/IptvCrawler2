<?php
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class IPTV
{
    private $client;
    public  $countries;
    public function __construct()
    {

        $this->init_client();
        $this->countries=$this->read_countries();
    }

    private function  read_countries(){
        $csv=file_get_contents(ABS_PATH.'/database/countries.csv');
        $lines=explode("\r\n",$csv);
        $countries=[];
        foreach ($lines as $line){
            if (strlen($line)<5)continue;
            $row=explode(',',$line);
            $countries[$row[1]]=$row[0];
        }
        return $countries;
    }

    private function init_client(){
        $this->client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://iptvcat.com/',
            // You can set any number of default request options.
            'timeout'  => 60,
            'verify' => false
        ]);
    }

    public function Fetch(){

        $channels=[];
        foreach (COUNTRIES as $country){

            $this->init_client();
            $country=$this->countries[$country];
            $page=1;
            $cnt=0;
            while (true){

                $res=$this->client->get($country.'/'.$page, [
                    'headers'        => ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0']
                ]);

                $page++;
                usleep(10000);
                echo $page;
                if ($res->getStatusCode()==200){
                    $content=$res->getBody()->getContents();
                    if (strpos($content, 'Nothing found!')!== false)
                    {
                        echo 'ddd';
                        break;
                    }

                    preg_match_all("'<span[^><]+?data-stream=\"([0-9]+?)\">Add to list</span>'si",$content,$ids);
                    preg_match_all("'<div class=\"live green\"[^><]+?>([0-9]+?)</div>'si",$content,$lives);
                    preg_match_all("'class=\"state ([^\"]+?)\"'si",$content,$statuses);

                    for ($i=0;$i<count($ids);$i++){

                        if (STATUS == 'disable' && $statuses[1][$i] != "online") continue;

                        if (STATUS == 'enable' && $statuses[1][$i] != "Offline") continue;

                        $live = intval($lives[1][$i]);

                        if ($live < MIN_QUALITY || $live > MAX_QUALITY) continue;

                        if (array_key_exists($ids[1][$i],$channels)) continue;

                        $channels[$ids[1][$i]]=[$live];

                        $res=$this->client->post('ajax/streams_a?action=list', [
                            'headers'        => [
                                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0',
                                'Referer'=> 'https://iptvcat.com/'.$country."/" . $page
                            ],
                            'form_params' =>[
                                'to_del' => 'false',
                                'sort' => 'false',
                                'items[]'=>$ids[1][$i]
                            ]
                        ]);

                        if (++$cnt >= MAX_CHANEL) break;
                    }

                }else throw new Exception("main:response wrong status=>" .$res->getStatusCode());

            }

            //$this->saver($country,1);
        }
    }

    private function saver($country,$page){
        $res=$this->client->get($country.'/'.$page, [
            'headers'        => ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0']
        ]);

        if ($res->getStatusCode()==200){
            $content=$res->getBody()->getContents();

            preg_match_all("'href=\"[^\"]*?my_list/([^\"]+?)\"[^<>]+?title=\"Download list\"'si",$content,$links);

            $res=$this->client->get("my_list/" . $links[1][0], [
                'headers'        => ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0']
            ]);

            $content=$res->getBody()->getContents();
            var_dump($content);
            exit();
        }
        var_dump('dd',$res);
        exit();
    }
}