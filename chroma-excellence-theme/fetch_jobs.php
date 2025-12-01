<?php
$url = "https://app.acquire4hire.com/careers/list.json?id=4668";
$response = file_get_contents($url);
echo $response;
?>
