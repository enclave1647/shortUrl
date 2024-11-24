<?php

namespace shorturl;

use shorturl\classes\DBAction;

require_once './classes/DBAction.php';

// Вывод ошибок PHP
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);


// Принимаемый короткий URL (код без домена)
$shortUrl = "";

$shortUrl = trim($_GET['short']);

// Опции SQL-запроса
$options = [
    // Выбираем оригинальный URL (по переданному короткому коду)
    "sql" => "SELECT origin_url as origin FROM urls WHERE short_url = :short",
    // Параметры для подготовленного запроса
    "params" => [
        // Передаем в запрос короткий URL (код без домена)
        "short" => $shortUrl
    ]
];

// Адрес для редиректа
$urlToRedirect = "";

// Массив с результатом SQL-запроса
$arrResult = array();
// Выполняем SQL-запрос с переданными параметрами, получаем результат как массив
$arrResult = DBAction::query($options)->as_array(); // (?array)

// Если есть результат SQL-запроса
if ($arrResult !== null)
    // Получаем оригинальный URL
    // [0] - первая строка, ['origin'] - столбец
    $urlToRedirect = $arrResult[0]['origin'];

// Иначе (если введенного в адресную строку короткого URL нет в БД), будем переходить на главную
else {

    // Определяем текущий протокол (HTTPS/HTTP)
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

    // Формируем ссылку на главную
    $urlToRedirect = $scheme . '://' . $_SERVER['HTTP_HOST'] . '/';
}

// Перенаправляем на сформированный URL (Оригинальный URL или Главная)
header("Location: $urlToRedirect");