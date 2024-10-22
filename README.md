## Тестовое задание

1. Посетитель сайта вводит любой оригинальный URL-адрес в поле ввода, как http://domain/любой/путь/ и т. д.
2. Нажимает кнопку submit
3. Страница делает ajax-запрос на сервер и получает уникальный короткий URL-адрес
4. Короткий URL-адрес отображается на странице как http://yourdomain/abCdE (не используйте внешние интерфейсы как goo.gl и т. д.)
5. Посетитель может скопировать короткий URL-адрес и повторить процесс с другой ссылкой

Короткий URL должен уникальным, перенаправлять на оригинальную ссылку и быть актуальным навсегда, неважно, сколько раз он был использован.

Требования:
1. Использовать PHP, JS, PostgreSQL или MySQL на выбор (желательно PostgreSQL)
2. Можно использовать JQuery, Bootstrap
3. Не использовать другие фрэймворки
----------------------


#### Дата поступления: *17.10.2024*
#### Дата выполнения: *21.10.2024*

#### Затраченное время: *17ч*

----------------------

<br>

*Генератор коротких URL*
======================

### Используемые технологии - *<u>PHP 8.3, MySQL 5.7, JS</u>*


[short.tg-apps.ru()](http://short.tg-apps.ru/)
----------------------

<br>


*<u>Описание БД</u>*
----------------------

В БД находится одна таблица ```urls```:

```mysql
CREATE TABLE urls (
  id INT PRIMARY KEY AUTO_INCREMENT,
  origin_url VARCHAR(255) UNIQUE NOT NULL,
  short_url VARCHAR(7) UNIQUE NOT NULL
);
```

Для столбцов ```origin_url``` и ```short_url``` установлено ограничение уникальности ```UNIQUE```.\
Это предотвращает вставку значений, которые уже существуют в таблице.

Оба столбца обязательные к заполнению - ```NOT NULL```.\
Это предотвращает ошибочную вставку в таблицу пустых значений.

<br>

*<u>Описание клиентской части*</u>
----------------------

### 1. Валидация

Поле ввода оригинального URL проверяется на непустые значения.\
Также, присутствует простая проверка регулярным выражением:

```javascript
new RegExp('^https?:/{2}', 'i');
```


### 2. AJAX-отправка

По кнопке <u>***Submit***</u> происходит AJAX-оправка прошедшего валидацию
значения оригинального URL на сервер.

Используется метод ```fetch()```. 

### 3. Вывод сообщений с сервера

При успешном получении короткой ссылки, либо при возникновении ошибки
происходит возврат и отображение сообщения с сервера.  

<br>

*<u>Описание серверной части</u>*
----------------------

### За основную логику приложения отвечает класс ```ShortUrlCreator```

<br>

Его основными задачами являются:
1. Проверка, есть ли в БД переданный URL

```php
private function checkUrlInDB(): bool
```       

2. Создание генератора случайной буквенной последовательности

```php
private function createShortCodeGen(): callable
```    

3. Добавление пары "оригинальный URL + короткий код" в БД

```php
private function addUrlsInDB(): void
```

4. Формирование и возвращение короткого URL (в объекте класса ```Response```) 
<br>
   (в случае необходимости, создание кода для короткого URL и проверка его на уникальность в БД)


```php
public function getShortUrl(): Response
```

<br>

### Второстепенная логика приложения
### Основана на служебных классах ```DBAction``` и ```Response```. 

<br>

### Класс ```DBAction```
##### Реализует паттерн Singleton (Одиночка).
Является оберткой для встроенного класса ```PDO```.\
Реализован частично статическим для уменьшения зависимости
от основного класса ```ShortUrlCreator```.



Его основными задачами являются:
1. Выполнение SQL-запроса на объекте класса ```PDO``` с переданными параметрами

```php
public static function query(array $options): DBAction
```       

2. Возврат полученных данных в виде ассоциативного массива строк (либо ```null```) 
   
```php
public function as_array(): ?array
```    

Метод ```fetchAll()``` класса ```PDOStatement``` не предоставляет возможности
получить весь набор результатов SQL-запроса в виде ассоциативного массива.
Можно лишь получить следующую строку ```fetch()```.

3. Возврат количества затронутых строк (в последнем SQL запросе)

```php
public function rowCount(): int
```

Также есть вспомогательные методы:
```php
private static function getInstance(): DBAction // Получение экземпляра класса
public static function close(): void // Закрытие соединения с БД
private function __construct() // Приватный конструктр
```

<br>

### Особенности использования класса ```DBAction```
Данный класс, будучи оберткой класса ```PDO```,
обеспечивает более удобное выполнения SQL-запросов (обычных и подготовленных)
с вынесением логики работы (с классом ```PDO```) в собственные методы.

Также, будучи частично статическим, позволяет выполнять SQL-запросы
на базе объекта ```PDO``` и получать данные без необходимости 
создавать собственный экземпляр.

Для выполнения SQL-запроса достаточно вызвать статический метод

```php
public static function query(array $options): DBAction
``` 

передав в него массив параметров ```$options```.

Например:

```php
$options = [
            "sql" => "SELECT id FROM urls WHERE origin_url = :origin",
            // Параметры для подготовленного запроса
            "params" => [
                "origin" => $this->originUrl
            ]
        ];
        
        return (bool) DBAction::query($options)->rowCount();

        // Если необходимо, можно вернуть полученное значение
        //return DBAction::query($options)->as_array()[0]['id'];
```
или

```php
        $options = [
            "sql" => "INSERT INTO urls SET origin_url = :origin, short_url = :short",
            "params" => [
                "origin" => $this->originUrl,
                "short" => $this->shortCode
            ]
        ];

        DBAction::query($options);
        
        // Если необходимо, можно вернуть кол-во затронутых строк (1)
        //return DBAction::query($options)->rowCount();
```

Логика метода ```query()``` подставит массив параметров и безопасно выполнит подготовленный запрос.\
Если необходимо выполнить обычный запрос, то достаточно передать пустой массив в ```"params"```,
либо не передавать ```"params"``` вовсе.

За безопасность отвечают блоки ```try-catch```, которые, в случае возникновения ошибки, возвращают
сообщение клиенту на базе объекта класса ```Response``` с возможностью выхода ```die().```

Например:

```php
    try {
        // ...
        // Действия с объектами PDO, PDOStatement 
        // ...
    } catch (PDOException $e) {
            // В случае возникновения ошибки
            // Создается объект ответа, сообщение (в виде JSON) отправляется клиенту
            // Скрипт завершает работу
            (new Response(
                  status: false
                , message: "Ошибка при выполнении SQL-запроса. " . $e->getMessage()
            ))->prepare()->send(die: true);
        }
```

.htacess
----------------------

```
RewriteEngine On
RewriteBase /

# Перенаправление с русских символов в запросе
# и с каталогов "/" на главную
RewriteCond %{REQUEST_URI} [А-я/]+
RewriteRule [А-я]+ http://%{HTTP_HOST}/

# Перенаправление с прочих запросов на redirect.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^([a-zA-Z0-9]+)/?$ http://%{HTTP_HOST}/redirect.php?short=$1 [L]

# Перенаправление с http на https
# RewriteCond %{HTTPS} off
# RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI} [R=301]
```
