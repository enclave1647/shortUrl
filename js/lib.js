// export function getShortUrl(longUrl) {
//     // Отправляем на сервер оригинальный URL (в JSON)
//     return fetch('/getShortUrl.php', {
//        method: 'POST',
//        headers: {
//            'Content-Type': 'application/json; charset=UTF-8',
//        },
//        body: JSON.stringify({longUrl: longUrl})
//     // Приводим ответ от сервера в object (из JSON в object)
//     }).then(response=> response.json());
// }

// Функция (обертка для fetch()) отправки запроса на сервер
export function sendRequest(options) {
    // Отправляем на сервер оригинальный URL (в JSON)
    return fetch(options.url, options.init)
        // Приводим ответ от сервера в object (из JSON в object)
        .then(response=> response.json());
}