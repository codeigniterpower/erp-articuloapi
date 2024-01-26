#!/usr/bin/php
<?php

$login    = 'diazvictor';
$password = 'vitronic';
$cod_item = '20231123';
$url      = 'http://api.me/file/get/' . $cod_item;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
$result = curl_exec($ch);
curl_close($ch);

echo($result);
echo(PHP_EOL);
