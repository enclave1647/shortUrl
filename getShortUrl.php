<?php

namespace shorturl;

use shorturl\classes\ShortUrlCreator;

require_once './classes/ShortUrlCreator.php';

// Вывод ошибок PHP
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Получаем параметры POST-запроса в массив (из JSON)
$arrRequest = json_decode(file_get_contents('php://input'), true);

// Если "longUrl" не определен - выходим
(isset($arrRequest['longUrl'])) or die('Входящий параметр "longUrl" не определен');

// Оригинальный URL
$originUrl = "";
// Объект пользовательского класса Response
$response = "";

// Подготавливаем переданный URL
$originUrl = trim($arrRequest['longUrl']);

// Создаем экземпляр генератора коротких URL
$urlCreator = new ShortUrlCreator($originUrl);

// Получаем короткий URL в объекте Response
$response = $urlCreator->getShortUrl();

// Подготавливаем объект ответа (формируем JSON), отправляем и выходим
$response->prepare()->send(die: true);