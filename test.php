<?php

$res=file_get_contents('http://localhost/last.json');

$data=json_decode($res,true);

echo $data['link'];
echo "\n<br>";
echo $data['Github'];
echo "\n<br>";
print_r($data);
