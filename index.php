<!doctype html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="icon" href="/images/favicon.png">
        <link rel="stylesheet" href="/styles/reset.css">
        <link rel="stylesheet" href="/styles/style.css">
        <title>ShortUrls</title>
    </head>
    <body>
        <main class="main">
            <div class="container">
                <h1 class="main__title">Генератор коротких URL</h1>
                <div class="main__wrapper">
                    <div class="input__wrapper">
                        <? //<label for="longUrl">URL</label>?>
                        <input id="longUrl" class="main__input main__input_origin" type="url" placeholder="Введите URL...">
                    </div>
                    <div class="input__wrapper">
                        <label for="shortUrl">Короткий URL</label>
                        <input id="shortUrl" class="main__input main__input_short" type="url" disabled>
                    </div>
                    <div class="msg">
                        <div class="msg__wrapper">
                            <div id="msg" class="msg__text"></div>
                        </div>
                    </div>
                </div>
                <div id="btnSubmit" class="main__button">Submit</div>
            </div>
            <section class="links-table">
                <table class="links-table__table">
                    <tr class="links-table__tr">
                        <th class="links-table__th">№</th>
                        <th class="links-table__th">Оригинальная ссылка</th>
                        <th class="links-table__th">Короткая ссылка</th>
                    </tr>
                    <tr class="links-table__tr">
                        <td class="links-table__td">1</td>
                        <td class="links-table__td">https://developer.mozilla.org/ru/docs/Web/API/Node/textContent</td>
                        <td class="links-table__td">uCdxldR</td>
                    </tr>
                    <tr class="links-table__tr">
                        <td class="links-table__td">2</td>
                        <td class="links-table__td">https://developer.mozilla.org/ru/docs/</td>
                        <td class="links-table__td">GNdYdIA</td>
                    </tr>
                    <tr class="links-table__tr">
                        <td class="links-table__td">3</td>
                        <td class="links-table__td">https://wordsmall.ru/html-i-css/primery-stili-knopok-css.html</td>
                        <td class="links-table__td">arcBpnF</td>
                    </tr>
                    <tr class="links-table__tr">
                        <td class="links-table__td">4</td>
                        <td class="links-table__td">https://developer.mozilla.org/ru/docs/s</td>
                        <td class="links-table__td">rHvzuql</td>
                    </tr>
                    <tr class="links-table__tr">
                        <td class="links-table__td">5</td>
                        <td class="links-table__td">https://developer.mozilla.org/ru/</td>
                        <td class="links-table__td">OMKlLDp</td>
                    </tr>
                </table>
            </section>
        </main>
        <footer class="footer">
            <div class="container">
                <div class="footer__text">© 2024 enclave1647</div>
            </div>
        </footer>

    <script src="/js/main.js" type="module"></script>
    </body>
</html>