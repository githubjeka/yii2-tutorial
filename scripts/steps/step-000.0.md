###Знакомство с шаблоном приложения Advanced

Для перехода к следующему упражнению, выполните команду из директории yii2-tutorial

```
git checkout -f step-0
```

В последствии будет установлен "Шаблон приложения advanced", который станет доступен по 
<a href="/yii2-app-advanced/frontend/web/" target="_blank">ссылке</a>.

<p class="alert alert-info">
Пожалуйста, ознакомьтесь с <a href="https://github.com/yiisoft/yii2-app-advanced/blob/master/docs/guide/README.md" target="_blank">
официальным руководством</a>, для того чтобы иметь представление, как устроен "Шаблон приложения advanced".
</p>

Все статичные страницы нашего приложения не требуют каких-либо данных. А вот страница `Signup`(регистрация пользователей)
требует подключения к базе данных. Если на <a href="/yii2-app-advanced/frontend/web/index.php?r=site%2Fsignup" target="_blank">
этой странице</a> ввести в поля какие-либо данные и нажать кнопку "Signup", то скорее всего увидите ошибку `Database Exception...`.

Сейчас наш сайт пытается подключится к базе данных `yii2advanced` MySQL. Yii не ограничивает вас в выборе базы данных, вы
можете легко изменить базу данных, будь то MySQL, MSSQL, PostgreSQL или другие. Для обучения будем использовать
<a href="https://ru.wikipedia.org/wiki/SQLite" target="_blank">SQLite</a>, так как она компактная и не требует накладных
расходов. Знать тонкости синтаксиса SQLite не придётся, так как в большинстве случаев вместо SQL будет использоваться
<a href="https://ru.wikipedia.org/wiki/ORM">ORM</a> подход.

<p class="alert alert-warning">Обратите внимание, что для работы PHP и SQLite потребуется подключение php_pdo_sqlite.
<a href="/scripts/helpers/check.php" target="_blank">Проверьте подключено ли оно у вас.</a>
</p>

Поменяем настройки подключения к базе данных для всего сайта:

Перейдите в директорию `/yii2-app-advanced/common/config/`, в которой хранятся файлы конфигурации для работы всех 
(клиентской, административной, и других) частей сайта. В файле `main-local.php`:

```php
<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=yii2advanced',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];
```


Компонент `mailer`(компонент отправки почты) оставим без изменений. А вот настройки компонента `db` изменим.

<p class="alert alert-info">Подробнее о компонентах в 
<a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/structure-application-components.md" target="_blank">
официальном руководстве</a>
</p>

```php
<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'sqlite:' . __DIR__  .'/../../sqlite.db',
        ],       
    ],
];
```
В нашем и предыдущем случае за соединение с базой данной отвечает класс `yii\db\Connection`.

<p class="alert alert-info">Рекомендуется ознакомится с <a href="http://www.yiiframework.com/doc-2.0/yii-db-connection.html" target="_blank">
API класса Connection</a>
</p>

Для соединения нужно указать DSN. В случае с SQLite это путь к файлу `/yii2-app-advanced/sqlite.db`. Для остальных 
баз данных, наподобие:

```
'dsn' => 'pgsql:host=localhost;port=5432;dbname=mydatabase', // PostgreSQL
'dsn' => 'cubrid:dbname=demodb;host=localhost;port=33000', // CUBRID
'dsn' => 'sqlsrv:Server=localhost;Database=mydatabase', // MS SQL Server, sqlsrv
'dsn' => 'dblib:host=localhost;dbname=mydatabase', // MS SQL Server, dblib driver
'dsn' => 'mssql:host=localhost;dbname=mydatabase', // MS SQL Server, mssql driver
'dsn' => 'oci:dbname=//localhost:1521/mydatabase', // Oracle
```

> Имя источника данных (DSN) - это логическое имя, которое используется ODBC (Open Database Connectivity), чтобы 
> обращаться к диску и другой информации, необходимой для доступа к данным.

После настройки подключения, необходимо наполнить базу данных исходной информацией. Для это будем использовать "миграции". 
Для чего нужны миграции? Вот сейчас необходимо заполнить базу данных: создать таблицы, вставить начальные данные. И чтобы 
не описывать sql запросы, которые вы должны выполнить, была создана одна миграция. Всё что вам нужно сделать, это выполнить
консольную команду всё в той же директории `yii2-app-advanced`:
 
```
 php yii migrate
```
 
После этого увидите, что-то вроде: 
 
```
yii2-tutorial\yii2-app-advanced>php yii migrate
Yii Migration Tool (based on Yii v2.0.3)

Total 1 new migration to be applied:
     m130524_201442_init

Apply the above migration? (yes|no) [no]:y
*** applying m130524_201442_init
 > create table {{%user}} ... done (time: 0.059s)
*** applied m130524_201442_init (time: 0.111s)


Migrated up successfully.
```
 
Теперь в `yii2-app-advanced` можно обнаружить файл `sqlite.db` - это и есть SQLite база данных.

<p class="alert alert-info">
Для своего удобства можете воспользоваться бесплатной <a href="http://sqlitebrowser.org/" target="_blank">программой просмотра SQLite</a>.
Просто откройте файл sqlite.db с помощью этой программы, но учтите, что это может стать причиной блокировки работы 
с этим файлом для нашего руководства.
</p>

Ну что ж, вернёмся на <a href="/yii2-app-advanced/frontend/web/index.php?r=site%2Fsignup" target="_blank">Signup</a>
и попробуем ввести регистрационные данные: `Username` - `admin`, `Email` - `admin@local.net`, `Password` - `123456`.
После отправки данных, произойдёт переход на главную страницу с последующей аутентификацией пользователя `admin`. Сейчас 
мы находимся в пользовательском приложении (frontend). Шаблон `Advanced` также реализует административное приложение(backend).
Чтобы попасть в него, просто перейдите по <a href="/yii2-app-advanced/backend/web/" target="_blank">ссылке</a>. 
На данный момент backend скуден по функционалу, чем frontend. Далее постараемся исправить эту ситуацию.