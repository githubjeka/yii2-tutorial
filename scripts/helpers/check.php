<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Учебник по Yii2: <?= $step['title'] ?></title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootswatch/3.3.4/readable/bootstrap.min.css">
</head>
<body class="container">

<?php if (!extension_loaded('php_pdo_sqlite')) { ?>
    php_pdo_sqlite подключено.
<?php } else { ?>
    <p>php_pdo_sqlite не подключено.</p>
    <p>В php.ini нужно:</p>
    <ul>
        <li>Для Windows раскомментировать строку "extension=php_pdo_sqlite.dll"</li>
        <li>Для Linux подключить файл конфигурации "php_pdo_sqlite.ini"</li>
        <li>Или в google ищите по запросу - php_pdo_sqlite как подключить</li>
    </ul>

<?php } ?>

</body>
</html>
