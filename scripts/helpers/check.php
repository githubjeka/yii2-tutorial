<?php if (!extension_loaded('php_pdo_sqlite')) { ?>
    php_pdo_sqlite подключено.
<?php } else { ?>
    <p>php_pdo_sqlite не подключено.</p>
    <p>В php.ini нужно:</p>
    <ul>
        <li>Для Windows разкоментировать строку "extension=php_pdo_sqlite.dll"</li>
        <li>Для Linux подключить конфиг "php_pdo_sqlite.ini"</li>
        <li>Или в google ищите по запросу - php_pdo_sqlite как подключить</li>
    </ul>

<?php } ?>
