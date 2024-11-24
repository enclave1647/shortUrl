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
                        <?//<label for="longUrl">URL</label>?>
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
        </main>
        <footer class="footer">
            <div class="container">
                <div class="footer__text">© 2024 enclave1647</div>
            </div>
        </footer>

    <script src="/js/main.js" type="module"></script>
    </body>
</html>