import {getShortUrl} from './lib.js';

document.addEventListener('DOMContentLoaded', () => {

    // Получаем кнопку отправки "Submit"
    const submitBtn = document.getElementById('btnSubmit');

    // Получаем Input ввода оригинального URL
    const longUrlInp = document.getElementById('longUrl');

    // Получаем Input короткого URL
    const shortUrlInp = document.getElementById('shortUrl');

    // Получаем блок для сообщений от сервера
    const msgDiv = document.getElementById('msg');

    // Добавляем обработчик на Input ввода оригинального URL (input)
    longUrlInp.addEventListener('input', (e) => {

        // Получаем Input
        const input = e.currentTarget;

        // Получаем введенное значение
        // (пробелы - это тоже пусто (trim))
        const inpValue = input.value.trim();

        // Если Input после ввода пустой
        if (inpValue === '') {
            input.classList.add('wrong');
        } else input.classList.remove('wrong');
    })

    // Добавляем обработчик на кнопку Submit (click)
    submitBtn.addEventListener('click', () => {

        // Получаем значение оригинально URL из Input
        const longUrl = longUrlInp.value.trim();

        // TODO: +++ сделать валидацию введенного URL

        // TODO: --- сделать проверку на рабочий URL
        // TODO: проверку со стороны JS (через fetch или XMLHttpRequest)
        // TODO: блокирует политика CORS (можно сделать через сервер)

        // Очищаем Input для короткого URL
        shortUrlInp.value = '';
        // Убираем класс 'active' у Input для короткого URL
        shortUrlInp.classList.remove('active');
        // Блокируем Input для короткого URL
        shortUrlInp.disabled = true;

        // Очищаем блок сообщений от сервера
        msgDiv.textContent = '';

        // Если при клике на Submit введенного URL нет
        if (!longUrl)
            // Добавляем placeholder
            longUrlInp.placeholder = 'Введите URL...';
        // Иначе если введенный URL начинается не с 'http[s]://'
        else if (new RegExp('^https?:/{2}', 'i').test(longUrl) === false) {
            // Сбрасываем введенное значение
            longUrlInp.value = '';
            // Выводим placeholder
            longUrlInp.placeholder = 'Введите корректный URL...';
        // Иначе - валидация пройдена
        // Получаем короткий URL с сервера
        } else getShortUrl(longUrl)
                // После получения ответа
                .then(response => {
                    // Выводим ответ в консоль
                    console.log(response);

                    // Если статус ответа от сервера - true
                    // (короткий URL получен)
                    if (response.status === true) {
                        // Переносим полученный короткий URL в Input
                        shortUrlInp.value = response.data.shortUrl;
                        // Убираем disabled у Input
                        shortUrlInp.disabled = false;
                        // Добавляем класс 'active' к Input с коротким URL
                        shortUrlInp.classList.add('active');

                        // Если у блока сообщений есть класс 'error'
                        // (ранее была ошибка, сейчас нет)
                        if (msgDiv.classList.contains('error'))
                            // Убираем класс 'error'
                            msgDiv.classList.remove('error');
                        // Иначе, если status !true
                    } else {
                        // Добавляем класс 'error' к блоку сообщений
                        msgDiv.classList.add('error');
                    }

                    // Выводим сообщение от сервера
                    msgDiv.textContent = response.msg;
                });

    });

});



