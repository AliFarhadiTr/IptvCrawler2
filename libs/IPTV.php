<?php

use GuzzleHttp\Client;

class IPTV
{
    private $client;
    public $countries;
    public $total;
    public $outfile;

    public function __construct()
    {
        $this->countries = $this->read_countries();
        $this->OutputInit();
    }

    private function OutputInit()
    {
        $this->outfile = ABS_PATH . "/output/".intval(date("d"))."-channels.m3u";
        if (file_exists($this->outfile)) {
            unlink($this->outfile);
        }

    }

    private function read_countries()
    {
        $csv = file_get_contents(ABS_PATH . '/database/countries.csv');
        $lines = explode("\n", $csv);
        $countries = [];
        foreach ($lines as $line) {
            if (strlen($line) < 5) continue;
            $line=str_replace("\r",'',$line);
            $row = explode(',', $line);
            $countries[$row[1]] = $row[0];
        }
        return $countries;
    }

    private function init_client()
    {
        $this->client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://iptvcat.com/',
            // You can set any number of default request options.
            'timeout' => 60,
            'verify' => false,
            'cookies' => true
        ]);

    }

    public function Fetch()
    {

        $channels = [];
        $this->total = 0;
        $this->OutputInit();

        foreach (COUNTRIES as $country_txt) {

            $this->init_client();
            $country_value = $this->countries[$country_txt];
            $page = 1;
            $cnt = 0;
            while (true) {

                $err = 0;
                er1:
                if ($err > 5) break;
                try {

                    $res = $this->client->get($country_value . '/' . $page, [
                        'headers' => ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0']
                    ]);

                } catch (\Exception $ex) {
                    $err++;
                    sleep(rand(1, 4));

                    goto er1;
                }

                $page++;

                if ($res->getStatusCode() == 200) {
                    $content = $res->getBody()->getContents();

                    if (strpos($content, 'Nothing found!') !== false) {

                        break;
                    }
					if (isset($res->getHeaders()['Content-Type'][0]) && strpos($res->getHeaders()['Content-Type'][0], 'video/mp4') !== false) {

                        die('blocked!');
                    }
                    preg_match_all("'<span[^><]+?data-stream=\"([0-9]+?)\">Add to list</span>'si", $content, $ids);
                    preg_match_all("'<div class=\"live green\"[^><]+?>([0-9]+?)</div>'si", $content, $lives);
                    preg_match_all("'class=\"state ([^\"]+?)\"'si", $content, $statuses);

                    for ($i = 0; $i < count($ids[1]); $i++) {

                        if (STATUS == 'disable' && $statuses[1][$i] != "Offline") {
                            continue;
                        }

                        if (STATUS == 'enable' && $statuses[1][$i] != "online"){
                            continue;
                        }

                        $live = intval($lives[1][$i]);

                        if ($live < MIN_QUALITY || $live > MAX_QUALITY) continue;
                        if (array_key_exists($ids[1][$i], $channels)) continue;
                        $channels[$ids[1][$i]] = [$live];

                        $err = 0;
                        er2:
                        if ($err > 3) break;
                        try {
                            $res = $this->client->post('ajax/streams_a?action=list', [
                                'headers' => [
                                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0',
                                    'Referer' => 'https://iptvcat.com/' . $country_value . "/" . $page
                                ],
                                'form_params' => [
                                    'to_del' => 'false',
                                    'sort' => 'false',
                                    'items[]' => $ids[1][$i]
                                ]
                            ]);

                        } catch (\Exception $ex) {
                            $err++;
                            sleep(rand(1, 4));
                            goto er2;
                        }

                        if (++$cnt >= MAX_CHANEL) break 2;
                    }

                } else {
                    $err++;
                    sleep(rand(1, 4));
                    goto er1;
                }

                sleep(DELAY_SEC);
            }

            $err=0;
            er3:
            if ($err>3)continue;
            try {
                $this->saver($country_txt, 1);
            }catch (\Exception $ex){
                $err++;
                goto er3;
            }

        }
        return $this->outfile;
    }

    private function saver($country_txt, $page)
    {

        $res = $this->client->get($this->countries[$country_txt] . '/' . $page, [
            'headers' => ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0']
        ]);

        if ($res->getStatusCode() == 200) {
            $content = $res->getBody()->getContents();

            preg_match_all("'href=\"[^\"]*?my_list/([^\"]+?)\"[^<>]+?title=\"Download list\"'si", $content, $links);

            $res = $this->client->get("my_list/" . $links[1][0], [
                'headers' => ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0']
            ]);

            $content = $res->getBody()->getContents();

            preg_match_all("'group-title=\"([^\"]*?)\"'si", $content, $titles);
            $cnt = count($titles[1]);
            $this->total += $cnt;

            if (file_exists($this->outfile)) {
                $content = str_replace("#EXTM3U\n", "", $content);
                $content = str_replace("#PLAYLIST:iptvcat.com\n", "", $content);

                $content = APPEND_ALL . "\n" . $content;

                if (ADD_CHANEL_COUNT) $content = preg_replace("'group-title=\"[^\"]*?\"'si", "group-title=\"{$country_txt}({$cnt})\"", $content);
                else $content = preg_replace("'group-title=\"[^\"]*?\"'si", "group-title=\"{$country_txt}\"", $content);

            } else {

                if (strpos($content, "#PLAYLIST:iptvcat.com\n") !== false) {

                    $content = str_replace("#PLAYLIST:iptvcat.com\n", "#PLAYLIST:iptvcat.com\n" .
                        APPEND_ALL . "\n", $content);

                    if (ADD_CHANEL_COUNT) $content = preg_replace("'group-title=\"[^\"]*?\"'si", "group-title=\"{$country_txt}({$cnt})\"", $content);
                    else $content = preg_replace("'group-title=\"[^\"]*?\"'si", "group-title=\"{$country_txt}\"", $content);

                    $content = str_replace("#PLAYLIST:iptvcat.com\n", "#PLAYLIST:iptvcat.com\n" .
                        APPEND_FIRST . "\n", $content);

                } else {
                    $content = str_replace("#EXTM3U\n", "#PLAYLIST:iptvcat.com\n" .
                        APPEND_ALL . "\n", $content);

                    if (ADD_CHANEL_COUNT) $content = preg_replace("'group-title=\"[^\"]*?\"'si", "group-title=\"{$country_txt}({$cnt})\"", $content);
                    else $content = preg_replace("'group-title=\"[^\"]*?\"'si", "group-title=\"{$country_txt}\"", $content);

                    $content = str_replace("#PLAYLIST:iptvcat.com\n", "#PLAYLIST:iptvcat.com\n" .
                        APPEND_FIRST . "\n", $content);
                }
            }

            $content = str_replace("#__COUNTRY__#", $country_txt, $content);

            for ($i = 0; $i < count(FIND_TEXT); $i++) {
                $content = str_replace(FIND_TEXT[$i], REPLACE_TEXT[$i], $content);
            }

            file_put_contents($this->outfile, $content, FILE_APPEND | LOCK_EX);

            /*
            $new_name = preg_replace("'[0-9]+?-channels\.m3u'si", $this->total . "-channels.m3u", $this->outfile);
            if ($new_name!=$this->outfile){
                if(file_exists($new_name))unlink($new_name); // Delete the existing file if exists
                rename($this->outfile, $new_name); // Rename the oldFileName into newFileName
            }

            $this->outfile = $new_name;
            */
        }else throw new \Exception("cant save file.");

    }
}