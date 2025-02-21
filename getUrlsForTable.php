<?php

namespace shorturl;

use shorturl\classes\DBAction;
use shorturl\classes\Response;

require_once './classes/Response.php';
require_once './classes/DBAction.php';

// Вывод ошибок PHP
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ERROR);

// TODO: 1. Получаем ссылки из БД

// Массив с полученными из БД данными
$arrDataFormDB = array();

// Опции подготовленного SQL-запроса
$options = array();

// Подготавливаем запрос
$options = [
    // Получаем все строки из таблицы urls
    "sql" => "SELECT * FROM urls",
];

// Выполняем подготовленный запрос
$arrDataFormDB = DBAction::query($options)->as_array();

// Если данные из БД не получены - выходим с сообщением
!empty($arrDataFormDB) or die('Данные из БД не получены');

// TODO: 2 Формируем объект Response для отправки

(new Response(
     status: true
   , message: 'Данные из БД получены успешно'
   , data: $arrDataFormDB
))->prepare()->send(die: true);


// TODO: 3 Отправляем полученные из БД ссылки клиенту

