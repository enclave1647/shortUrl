<?php

namespace shorturl\classes;

require_once './classes/DBAction.php';
require_once './classes/Response.php';
require_once './classes/Config.php';


/*** *************** Генератор коротких URL **************** ***
 *
 *** Проверяет, есть ли в БД переданный URL
 *** private checkUrlInDB(): bool
 *
 *** Создает генератор случайной буквенной последовательности
 *** private createShortCodeGen(): callable
 *
 *** Добавляет пару "оригинальный URL + короткий код" в БД
 *** private addUrlsInDB(): void
 *
 *** Возвращает сформированный короткий URL (в объекте класса Response)
 *** public getShortUrl(): Response
 *
 *** ******************************************************* ***/
class ShortUrlCreator
{
    // Оригинальный URL (string)
    private string $originUrl;

    // Короткий URL (null/string)
    private ?string $shortUrl;

    // Код для короткого URL (null/string)
    private ?string $shortCode;

    // Длина короткого URL (int)
    private int $codeLength;

    // Объект пользовательского класса Response
    // с содержанием параметров ответа
    private ?Response $response;

    public function __construct(string $originUrl) {
        // Записываем оригинальный URL
        $this->originUrl = $originUrl;
        // Инициализируем короткий URL в null
        $this->shortUrl = null;
        // Инициализируем код для короткого URL в null
        $this->shortCode = null;
        // Инициализируем объект ответа в null
        $this->response = null;

        // Длина короткого кода для URL
        // 7 знаков в диапазоне от a-z до A-Z (52 символа)
        // достаточно для 51^7 = 1,028 трлн значений
        $this->codeLength = 7;
    }

    // Проверяет, существует ли в БД оригинальный (переданный) URL
    private function checkUrlInDB(): bool {

        // Опции SQL-запроса
        $options = array();

        $options = [
            // Выбираем столбец id (для получения кол-ва строк по переданному URL (1/0))
            // SELECT COUNT(id)... не подошел, т.к. в случае отсутствия значения,
            // возвращается тоже строка - "0"
            "sql" => "SELECT id FROM urls WHERE origin_url = :origin",
            // Параметры для подготовленного запроса
            "params" => [
                // Передаем в запрос оригинальный URL
                "origin" => $this->originUrl
            ]
        ];

        // Возвращаем результат выполнения SQL-запроса (кол-во полученных строк)
        // приведенный к bool (1 - true / 0 - false)
        return (bool) DBAction::query($options)->rowCount();

    }

    // Создает и возвращает генератор случайной буквенной последовательности
    // (из заданного диапазона символов, заданной длинны)
    private function createShortCodeGen(): callable {

        /*** *** При множественной генерации кода *** ***/
        /*** *** данный блок выполняется единожды *** ***/

        // Создаем массив символов для генерации последовательности
        // (распаковываем (...) два массива последовательностей символов в один)
        $arrSymbols = [...range("a", "z"), ...range("A", "Z")];

        // Длина последовательности
        // (счетчик оставшихся символов)
        $codeLength = $this->codeLength;

        /*** **************************************** **/

        // Возвращаем функцию-генератор
        // (вместе со значениями использованных/наследованных (use) переменных)
        return function() use ($arrSymbols, $codeLength) {

            // Последовательность символов
            $shortCode = "";

            // Генерируем последовательность
            while ($codeLength !== 0) {

                // Получаем следующий символ из массива символов
                // (по случайному ключу в диапазоне от 0 до count(array))
                $shortCode .= $arrSymbols[mt_rand(0, count($arrSymbols) - 1)];

                // Уменьшаем счетчик оставшихся символов
                $codeLength--;
            }

            // Возвращаем полученную последовательность
            return $shortCode;
        };

    }

    // Добавляет пару URL (оригинальный + короткий) в БД
    private function addUrlsInDB(): void {

        // Опции SQL-запроса
        $options = [
            // Добавляем пару URL (оригинальный + короткий) в БД
            "sql" => "INSERT INTO urls SET origin_url = :origin, short_url = :short",
            "params" => [
                // Передаем в запрос оригинальный URL
                "origin" => $this->originUrl,
                // И сгенерированный короткий код
                "short" => $this->shortCode
            ]
        ];

        // Выполняем INSERT
        DBAction::query($options);

        // Можно получить кол-во затронутых строк (1)
        //DBAction::query($options)->rowCount();
    }

    /*** (Интерфейс пользователя) ***/
    // Возвращает короткий URL
    public function getShortUrl(): Response {

        /***
           *
           * В БД короткий URL храниться в виде сгенерированного кода (без домена)
           * определенной (в __construct()) длины
           *
        ***/

        // Инициализируем объект Response
        $this->response = new Response();

        // Опции SQL-запроса
        $options = array();

        // Если есть в БД короткий URL
        // (для переданного оригинального URL)
        if ($this->checkUrlInDB() === true) {

            $options = [
                // Выбираем короткий URL (по переданному оригинальному URL)
                "sql" => "SELECT short_url as short FROM urls WHERE origin_url = :origin",
                // Параметры для подготовленного запроса
                "params" => [
                    // Передаем в запрос оригинальный URL
                    "origin" => $this->originUrl
                ]
            ];

            // Выполняем запрос
            // Получаем данные в виде массива
            // Выбираем по ключам короткий код
            // ([0] - первая строка, ['short'] - столбец (значение короткого URL))
            $this->shortCode = DBAction::query($options)->as_array()[0]['short'];

            // Заполняем объект ответа
            $this->response->setStatus(true);
            $this->response->setMessage("URL извлечен из БД");
        }

        // Если в БД нет короткого URL
        // (оригинального URL тоже нет)
        else {

            // Создаем генератор буквенной последовательности
            $shortCodeGenerator = $this->createShortCodeGen();

            // Временная последовательность
            $tmpShortCode = "";

            do {
                // Генерируем последовательность
                $tmpShortCode = $shortCodeGenerator();

                // Опции SQL-запроса
                $options = [
                    // Ищем строку (UNIQUE) по сгенерированному коду
                    "sql" => "SELECT id FROM urls WHERE short_url = :short",
                    "params" => [
                        // Передаем код в запрос
                        "short" => $tmpShortCode
                    ]
                ];

            /*** Проверка на уникальность сгенерированного кода в БД ***/
            // Пока последовательность неуникальна в БД - повторяем do{}
            // (пока кол-во полученных SQL-запросом строк !== 0 (пока что-получили))

            // (если после первой генерации код уникален - do{} выполняется единожды)
            } while (DBAction::query($options)->rowCount() !== 0);

            // После генерации кода и проверки его на уникальность в БД
            // Переносим созданный код в свойство класса
            $this->shortCode = $tmpShortCode;

            // И добавляем пару Оригинальный URL + Короткий URL (код без домена) в БД
            $this->addUrlsInDB();

            // Заполняем объект ответа
            $this->response->setStatus(true);
            $this->response->setMessage("URL создан и добавлен в БД");
        }

        // Закрываем соединение с БД
        // (можно закрыть через деструктор класса)
        DBAction::close();

        // Определяем текущий протокол (HTTPS/HTTP)
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

        // Формируем короткий URL
        $this->shortUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . '/' . $this->shortCode;

        // Добавляем в объект ответа данные - короткий URL
        $this->response->setData(['shortUrl' => $this->shortUrl]);

        return $this->response;
    }

}

// TODO: +++ сделать закрытие соединения с БД (PDO)
// TODO: +++ Проверить наличие блоков try-catch-finally
// TODO: +++ Создать метод DBAction::close();
// TODO: --- Вынести параметры подключения в конфиг