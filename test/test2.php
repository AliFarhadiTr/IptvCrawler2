<?php

require_once '../last2.php';

$data=GetLastIPTVLinks();

echo $data['link'];
echo "\n<br>";
echo $data['Github'];
echo "\n<br>";
print_r($data);