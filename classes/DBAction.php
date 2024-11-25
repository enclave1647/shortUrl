<?php

namespace shorturl\classes;

use PDO;
use PDOException;
use PDOStatement;

/*** (реализует паттерн Singleton) ***/

class DBAction
{

    // Экземпляр класса DBAction
    private static ?DBAction $instance;

    // Объект подключения к БД (PDO)
    private ?PDO $dbh;

    // Объект подготовленного запроса к БД
    private ?PDOStatement $sth;

    // Приватный конструктор
    // (для реализации Singleton)
    private function __construct() {
        try {
            // Создаем подключение к БД (PDO)
            $this->dbh = new PDO("mysql:host=HOST;dbname=DB_NAME", "USER", "PASSWORD");

            // Инициализируем остальные переменные в null
            $this->sth = null;
        } catch (PDOException $e) {
            // В случае возникновения ошибки
            // Создаем объект ответа, отправляем клиенту и выходим
            (new Response(
                status: false
              , message: "Соединение с БД не установлено. " . $e->getMessage()
            ))->prepare()->send(die: true);
        }
    }

    /*** Реализация Singleton ***/
    // Возвращает единственный экземпляр класса DBAction
    private static function getInstance(): DBAction {

        // Если объект данного класса не существует
        if (!isset(self::$instance))
            // Создаем
            self::$instance = new DBAction();

        // Возвращаем созданный объект
        return self::$instance;
    }

    /*** (Интерфейс пользователя) ***/
    // Выполняет SQL-запрос с переданными параметрами
    // (на объекте PDO)
    public static function query(array $options): DBAction {

        // Получаем экземпляр текущего класса
        $dbAction = self::getInstance();

        // Переменные-опции SQL-запроса
        $sql = "";
        $params = array();

        // Деструктурируем входящий массив опций в переменные
        // (переносим значения из массива в переменные по ключам массива)
        ['sql' => $sql, 'params' => $params] = $options;

        try {
            // Подготавливаем SQL-запрос
            $dbAction->sth = $dbAction->dbh->prepare($sql);
            // Выполняем запрос
            // Передаем (привязываем к запросу) массив параметров
            // Например, "SELECT :number", $params = ['number' => 123] ===> SELECT 123;
            $dbAction->sth->execute($params);

        } catch (PDOException $e) {
            // В случае возникновения ошибки
            // Создаем объект ответа, отправляем клиенту и выходим
            (new Response(
                  status: false
                , message: "Ошибка при выполнении SQL-запроса. " . $e->getMessage()
            ))->prepare()->send(die: true);
        }

        // Возвращаем заполненный результатом выполнения SQL-запроса объект ($this)
        return $dbAction; // == return $this;

    }

    /*** (Интерфейс пользователя) ***/
    // Возвращает массив полученных строк, если результат выполнения SQL-запроса вернул данные (например, при SELECT)
    // Возвращает null, если результат выполнения SQL-запроса ничего не вернул (например, при INSERT)
    public function as_array(): ?array {

        // Временный массив для сбора полученных строк
        $arrTmp = array();

        // Получаем следующую строку
        // (пока есть строки)
        while ($row = $this->sth->fetch(PDO::FETCH_ASSOC)) {
            // Переносим строку во временный массив
            $arrTmp[] = $row;
        }

        // Возвращаем массив полученных строк или null
        return (!empty($arrTmp)) ? $arrTmp : null;
    }

    /*** (Интерфейс пользователя) ***/
    // Возвращает кол-во затронутых строк
    // (в последнем выполненном SQL-запросе)
    public function rowCount(): int {
        return $this->sth->rowCount();
    }

    /*** (Интерфейс пользователя) ***/
    // Закрывает соединение с БД
    // и уничтожает объекта класса
    public static function close(): void {
        // Закрываем соединение
        self::$instance->dbh = null;
        self::$instance->sth = null;

        // Уничтожаем объект
        self::$instance = null;
    }

}