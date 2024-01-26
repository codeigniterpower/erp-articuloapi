#!/usr/bin/php
<?php

$login = 'diazvictor';
$password = 'vitronic';
$url = 'http://api.me/api/v1/upload/';

$fields = [
  "cod_item" => "20231123",
  "cod_items_description" => "20231123eng",
  "cod_tipo" => "1",
  "is_set" => "0",
  "is_available" => "1",
  "is_managed" => "1",
  "is_activo" => "0",
  "fecha_manufactura" => "20220112",
  "pic_item_bin_main" => base64_encode(file_get_contents('Voucher-1536x901.jpg'))
];

//var_dump($fields);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
$data = curl_exec($ch);
curl_close($ch);
var_dump($data);

echo(PHP_EOL);
