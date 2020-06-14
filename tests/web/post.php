<?php
declare(strict_types = 1);

namespace App\Tests\web;

use App\Entity\piece\Queen;

require '../../vendor/autoload.php';

$id = 1;
$url = "http://127.0.0.1:8000/move/$id";

$post_data = array(
    "start" => "a8",
    "end" => "a2",
    "target" => Queen::ID
);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// Указываем, что у нас POST запрос
curl_setopt($ch, CURLOPT_POST, 1);
// Добавляем переменные
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

$output = curl_exec($ch);

curl_close($ch);

echo $output;