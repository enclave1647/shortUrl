<?php

namespace shorturl\classes;

/*** Класс, описывающий объект ответа от сервера ***/

/*** Можно использовать так:

    (new Response(
        status: false
      , message: "Соединение с БД не установлено. " . $e->getMessage()
    ))->prepare()->send(die: true);

Или так:

    $response = new Response();

    $response->setStatus(true);
    $response->setMessage("Сообщение...");
    $response->setData(['data' => 'что-то важное']);

    $response->prepare()->send();

***/

class Response
{
    private ?bool $status;

    private ?string $message;

    private ?array $data;

    private ?string $preparedJSON;

    /***
     * Поля объекта могут заполнятся через констуктор
     * или через методы-сеттеры
     ***/
    public function __construct($status = null, $message = null, $data = null)
    {
        // Инициализируем свойства в null
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
        $this->preparedJSON = null;
    }

    public function setStatus($status): void {
        $this->status = $status;
    }

    public function setMessage(string $message): void {
        $this->message = $message;
    }

    public function setData(array $data): void {
        $this->data = $data;
    }

    // Подготовка объекта к отправке клиенту
    public function prepare(): Response {

        $tmpArray = array();

        (!is_null($this->status)) ? $tmpArray['status'] = $this->status : '';
        ($this->message) ? $tmpArray['msg'] = $this->message : '';
        ($this->data) ? $tmpArray['data'] = $this->data : '';

        // Если в объекте ничего нет - готовим к возврату текст, иначе JSON
        $this->preparedJSON = (!empty($tmpArray)) ? json_encode($tmpArray) : "Response is empty...";

        return $this;
    }

    // Отправка подготовленных данных клиенту
    public function send($die = false): void {
        // Отправляем JSON клиенту
        echo $this->preparedJSON;
        // Если передан флаг $die = true
        if ($die === true)
            // Выходим
            die();
    }
}