### Работа с формами

В этом разделе рассмотрим как создать форму.

Чтобы начать, выполните команду из директории yii2-tutorial

```
git checkout -f step-0.2
```

Как выглядит форма, созданная с помощью Yii2,  можно увидеть <a href="/yii2-app-advanced/frontend/web/index.php?r=site%2Fcontact" target="_blank">по ссылке</a>.

Иногда можно увидеть на этой странице ошибку 

> "Invalid Configuration – yii\base\InvalidConfigException
Either GD PHP extension with FreeType support or ImageMagick PHP extension with PNG support is required.".

Связана она с тем, что на этой странице используется CAPTCHA и ей необходима <a href="http://php.net/manual/ru/image.installation.php" target="_blank">GD</a>
или <a href="https://php.net/manual/ru/imagick.installation.php" target="_blank">ImageMagick</a> PHP библиотеки.

Если всё в порядке, то продолжим. По адресу ссылки `index.php?r=site%2Fcontact`, где находится форма, можно увидеть, что используется
 всё тот же `SiteController` контроллер, что и в предыдущем уроке. Только тут действие другое - `contact`. 
Следовательно открываем `\frontend\controllers\SiteController::actionContact`:

```php
public function actionContact()
{
    $model = new ContactForm();
    if ($model->load(Yii::$app->request->post()) && $model->validate()) {
        if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('success', 'Спасибо за ваше письмо. Мы свяжемся с вами в ближайшее время.');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка отправки почты.');
        }

        return $this->refresh();
    } else {
        return $this->render('contact', [
            'model' => $model,
        ]);
    }
}
```

Видим знакомый уже `$this->render`, где первым параметром передаётся название вида - в данном случае используется вид
`'contact'`. Открываем его `yii2-app-advanced/frontend/views/site/contact.php`. 

Чтобы создать в Yii html код формы:

```html
<form action="..." method="post">
    <input ...>
    <input ...>
    <input ...>
    <button>
</form>
```

нужно обратиться за помощью к виджету `\yii\widgets\ActiveForm`. Наследник этого виджета - `yii\bootstrap\ActiveForm;`,
и используется в `contact.php`. Отличия `\yii\widgets\ActiveForm` от `yii\bootstrap\ActiveForm` в том, что последний
выводит элементы формы с учётом <a href="http://getbootstrap.com/css/#forms" target="_blank">требований Bootstrap</a>.
   
На первых порах возникает вопрос - зачем использовать этот виджет и вообще php код, если можно использовать HTML. Во-первых,
использование виджета ускоряет процесс создания рутинных, обычных форм, а во-вторых - делает легким процесс проверки пользовательских данных.
 
В `contact.php`

```php
<?php $form = ActiveForm::begin(['id' => 'contact-form', 'enableClientValidation' => false]); ?>
    <?= $form->field($model, 'name') ?>
    <?= $form->field($model, 'email') ?>
    <?= $form->field($model, 'subject') ?>
    <?= $form->field($model, 'body')->textArea(['rows' => 6]) ?>
    <?= $form->field($model, 'verifyCode')->widget(
        Captcha::className(),
        [
            'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
        ]
    ) ?>
    <div class="form-group">
        <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
    </div>
<?php ActiveForm::end(); ?>
```

Методы `ActiveForm::begin` и `ActiveForm::end();` выводят открывающий и закрывающий теги формы. Между этими методами 
с помощью метода ActiveForm::field создаются элементы формы.

<p class="alert alert-info">Рекомендуется ознакомится с <a href="http://www.yiiframework.com/doc-2.0/yii-bootstrap-activeform.html" target="_blank">
API класса ActiveForm</a>
</p>

Вы наверное обратили внимание, что в каждый элемент формы передаётся переменная $model. Yii использует 
<a href="https://ru.wikipedia.org/wiki/Model-View-Controller" target="_blank">MVC («модель-представление-контроллер»)</a>
шаблон проектирования приложения. Вы уже знакомы с контроллерами и представлениям, так вот переменная `$model` - это 
недостающее звено, класс который описывает данные и предоставляет методы для работы с этими данными.
В данном случае, модель описывает "деловое предложение" или "вопрос" от пользователя, т.е. "обратную связь". 
В Yii для работы с моделью реализован базовый класс <a href="http://www.yiiframework.com/doc-2.0/yii-base-model.html" target="_blank">
yii\base\Model</a>. Этот класс предоставляет методы, которые:

- помогают наполнять модель данными,
- читать данные из модели,
- проверять данные на корректность;

Объект модели создаётся в контроллере и передаётся в представление

```php
public function actionContact()
{
    $model = new ContactForm();
   
    return $this->render('contact', ['model' => $model,]);    
}
```

Откройте класс `ContactForm`, он находится в `yii2-app-advanced/frontend/models/`. 

<p class="alert alert-info">
Принято все модели располагать в директории `application/models/`, но вы всегда можете расположить их где
угодно с учётом <a href="http://www.php-fig.org/psr/psr-4/ru/" target="_blank">стандарта PSR-4</a>.
</p>

```php
class ContactForm extends Model
{
    public $name;
    public $email;
    public $subject;
    public $body;
    public $verifyCode;
    
    public function rules()
    {
    
    }
    
    public function attributeLabels()
    {
    
    }
    
    public function sendEmail($email)
    {
    
    }
```

Сначала перечисляются все атрибуты модели, это - имя пользователя($name), электронный адрес($email), тема 
сообщения($subject), само сообщение($body) и CAPTCHA(verifyCode). Эти атрибуты описывают модель - сущность "обратная связь".
Дальше идёт метод `rules()`, который используется для валидации, проверки атрибутов модели. Второй метод `attributeLabels`
используется для описания меток, маркировок атрибутов на понятном для человека языке. Обычно эти метки используются
в представлении для описания элементов форм или другого. Следующий метод `sendEmail` отвечает за отправку "обратной связи"
на электронный адрес администратора сайта.

#### Как всё работает? (упрощённо)
 
Запрос от пользователя поступает на входной скрипт `web/index.php`. В скрипте создаётся приложение `yii\web\Application`
с учётом конфигураций. Приложение определяет маршрут - контроллер и действие. Создаётся экземпляр контроллера и вызывается действие.
В действии создаёт модель и контроллер передаёт её в вид. Далее генерируется конечный ответ, с учётом шаблонов, видов и 
данных из моделей. Ответ отдаётся пользователю. Пользователь вводит данные и отправляет их опять в входной скрипт. Всё
повторяется до контроллера. Теперь в контроллере опять создаётся модель, но перед отправкой её в представление, она 
наполняется данными с помощью метода `yii\base\Model->load()` и затем данные проверяются методом `yii\base\Model->validate()`: 

```php
public function actionContact()
{
    $model = new ContactForm();
    if ($model->load(Yii::$app->request->post()) && $model->validate()) 
```

Модель наполняется пользовательскими данными  с помощью компонента request `Yii::$app->request->post()`. 
В первом уроке мы уже знакомились с одним из компонентном `db`, который служил для настройки
базы данных. Любой компонент приложения может быть вызван как `Yii::$app->имя_компонента`. Где `Yii::$app` - это приложение, 
которое было создано в входном файле `index.php`, а имя компонента можно установить через конфигурацию приложения.
Компонент `request` (служит для работы с HTTP запросами 
<a href="http://www.yiiframework.com/doc-2.0/yii-web-request.html" target="_blank">yii\web\Request</a>),
с помощью метода `post` возвращает `$_POST` данные, которые поступают в метод `$model->load`. 
Этот метод модели соотносит её атрибуты с данными из формы, по принципу 

```
имя_модели[атрибут] = данные[имя_элемента_формы][атрибут] 
```

В данном случае это:

```
ContactForm['name'] = $_POST['ContactForm']['name']
ContactForm['email'] = $_POST['ContactForm']['email']
// и так далее.
```

Данные из формы могут быть какими угодно, поэтому их следует проверить, перед тем как с ними работать. Одну часть работы
по проверке данных делает всё тот же метод `load`. Он определяет каким атрибутам можно задавать значения. Так если 
бы пользователь отправил из формы данные с именем $_POST['ContactForm']['hack'], то они бы не попали в модель, так как у
модели нет атрибута `$hack`. Также в `load` срабатывает внутренний механизм, который смотрит метод модели `rules` и извлекает из 
него все атрибуты которые там упоминаются. Если какого-то атрибута в `rules` нет, то атрибут модели считается не безопасным.

```php
public function rules()
{
    return [       
        [['name', 'email', 'subject', 'body'], 'required'],       
        ['email', 'email'],      
        ['verifyCode', 'captcha'],
    ];
}
```

Видно, что все возможные атрибуты модели встречаются в коде rules, поэтому в данном случае они все являются безопасными.
Т.е. для них выполняется условие:

```
имя_модели[атрибут] = данные[имя_элемента_формы][атрибут] 
```

Метод `$model->validate()`, который запускает вторую часть проверки данных, формирует из результата метода `rules()`
различные проверки. Делает он это по следующему принципу:

- перебирается каждый элемент массива:
```php
[   
    [['name', 'email', 'subject', 'body'], 'required'],
    ['email', 'email'],   
    ['verifyCode', 'captcha'],
];
```

- каждый элемент разбирается на составляющие: названия атрибутов, название валидатора.
 
`required` - валидатор, который проверяет отправил ли пользователь необходимые данные. Т.е. отправил ли пользователь 
 name, email, subject, body.
 
`email` - валидатор, который проверяет правильность введённого электронного адреса.

`captcha` - валидатор, который проверяет правильность введённого проверочного кода.

Список встроенных валидаторов можно посмотреть в 
<a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/tutorial-core-validators.md" target="_blank">
официальном руководстве</a> 
 
#### Пользовательские данные не корректные.
Если какая-нибудь проверка прошла не успешно, то атрибут модели модель `$errors` наполняется сообщениями об ошибках 
с учётом атрибута, в котором возникла ошибка. В результате в контроллере условие не выполняется: 

```php
if ($model->load(Yii::$app->request->post()) && $model->validate()) {
```

и в вид отправляется модель с сообщениями об ошибках. В виде `contract.php` с помощью `ActiveForm::field` выводятся
элементы формы: для тех у кого ошибки, формируются сообщения об ошибках, остальные выводятся со значениями.

<img src="/scripts/assets/screen0.2-1.jpg" class="img-responsive">

По умолчанию в виджете `ActiveForm` включено свойство `$enableClientValidation`, которое означает, что проверки выполняются
с помощью javascript кода прямо в браузере, а не отправляются на сервер. В нашем примере оно отключено, включите его
при создании ActiveForm:

```php
$form = ActiveForm::begin(['enableClientValidation' => true]);
```
  
Теперь при отправки данных, проверка будет происходить сначала в браузере, без отправки запросов на сервер:

<img src="/scripts/assets/screen0.2-2.jpg" class="img-responsive">


#### Пользовательские данные корректные.
Если же данные прошли проверку в контроллере:

```php
if ($model->load(Yii::$app->request->post()) && $model->validate()) {
```

то срабатывает метод `sendEmail` модели, который отправляет сообщение на электронный адрес администратора. Далее 
с помощью компонента <a href="http://www.yiiframework.com/doc-2.0/yii-web-session.html" target="_blank">yii\web\Session</a>
(отвечает за сессию $_SESSION пользователя) формируется статус-ответ об отправки почты. Дальше с помощью 
`Controller->refresh()` отправляется ответ пользователю, который содержит заголовки для обновления текущей страницы.

```php
if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
    Yii::$app->session->setFlash(
        'success',
        'Спасибо за ваше письмо. Мы свяжемся с вами в ближайшее время.'
    );
} else {
    Yii::$app->session->setFlash('error', 'Ошибка отправки почты.');
}

return $this->refresh();
```

### Создание формы.

Построим новую форму - опрос пользователя. Форма будет содержать следующие элементы:

- Ф.И.О.
- Пол
- Вопрос - какие планеты солнечной системы обитаемы?
- Вопрос - какие космонавты известны?
- Вопрос - на какую планету хотели бы полететь?
- Проверочный код - каптча

Форма опрос, в отличие от формы обратной связи, подразумевает под собой анализ ответов всех пользователей. Т.е нам
понадобится сохранять в базу данных и обрабатывать пользовательские данные. В случае с формой обратной связи пользовательские
данные только отправлялись на почту. Как вы помните, базовый класс `yii\base\Model` позволяет:

- наполнять модель данными,
- извлекать данные из модели,
- проверять данные на корректность;

Как видно его недостаточно для работы с базой данных. В Yii реализован популярный способ доступа к данным
<a href="https://ru.wikipedia.org/wiki/ActiveRecord" target="_blank">Active Record</a> - класс 
<a href="http://www.yiiframework.com/doc-2.0/yii-db-activerecord.html" target="_blank">yii\db\ActiveRecord</a>
Этот класс наследуется от базового класса `yii\base\Model`, расширяя его до такого состояния, при котором модель 
становится отражением **одной строки** в таблице из базы данных. Следовательно с моделью можно работать так же как со строкой в
базе данных - искать, создавать, изменять, удалять. Следующий код иллюстрирует всю мощь и красоту реализованного шаблона
проектирования Active Record в Yii:

```php
$model = new ActiveRecord;
$model->attributes = ['text' => 'Длинный текст', 'title' => 'Заголовок'];
$model->save();
```

С помощью трёх строк можно наполнить модель данными, проверить данные и в случае корректности, сохранить их в базу 
данных. Заметьте SQL не использовался, этот код сработает и для используемой нами SQLite и для любой другой СУБД.

И так вернёмся к форме "Опрос". Создадим для начала таблицу в базе данных.

Напомним, что для обращения к базе данных используется компонент, который мы настроили в 
`yii2-app-advanced/common/config/main-local.php` конфигурации приложения:

```php
'db' => [
    'class' => 'yii\db\Connection',
    'dsn' => 'sqlite:' . __DIR__  .'/../../sqlite.db',
],
```

и к нему можно обратиться через `\Yii::$app->db`. Познакомится с методами и свойствами компонента `db` можно в 
<a href="http://www.yiiframework.com/doc-2.0/yii-db-connection.html" targer="_blank">API класса yii\db\Connection</a>.

Для создания в базе данных таблицы, которая будет хранить данные из опросов, нам понадобится миграция. Сейчас она 
создана, вам её осталось только применить. 

Миграции создаются следующим образом:

```
yii2-tutorial\yii2-app-advanced> php yii migrate/create interview
Yii Migration Tool (based on Yii v2.0.3)

Create new migration '~/yii2-tutorial/yii2-app-advanced/console/migrations/m150428_104828_interview.php'? (yes|no) [no]:yes
New migration created successfully.
```

В `yii2-app-advanced/console/migrations` появится файл, наподобие `m150428_104828_interview.php`, который содержит класс
с тем же именем, что и имя файла. Этот класс содержит два метода `up()` и `down()`. Первый описывает, что происходит,
когда миграция применяется, второй - что происходит, когда миграция аннулируется. Код принято писать так, чтобы он работал
для любой СУБД, пусть то MySQL, PostgreSQL, SQlite или другая. Для того, чтобы писать универсальный код для всех СУБД
в Yii реализован <a href="http://www.yiiframework.com/doc-2.0/yii-db-schema.html" target="_blank">абстрактный класс yii\db\Schema</a>.
Этот класс описывает схему, как хранится информация в СУБД. При создании запроса определяется на основании `dns` компонента 
`yii\db\Connection`, какую схему нужно использовать. В свою очередь эта схема реализует работу с данными в зависимости от СУБД.
Кроме этого, класс `yii\db\Schema` содержит константы, которые позволяют описывать типы данных:

```php
/**
 * Поддерживаемые абстрактные типы данных для описания колонок.
 */
const TYPE_PK = 'pk';
const TYPE_BIGPK = 'bigpk';
const TYPE_STRING = 'string';
const TYPE_TEXT = 'text';
const TYPE_SMALLINT = 'smallint';
const TYPE_INTEGER = 'integer';
const TYPE_BIGINT = 'bigint';
const TYPE_FLOAT = 'float';
const TYPE_DOUBLE = 'double';
const TYPE_DECIMAL = 'decimal';
const TYPE_DATETIME = 'datetime';
const TYPE_TIMESTAMP = 'timestamp';
const TYPE_TIME = 'time';
const TYPE_DATE = 'date';
const TYPE_BINARY = 'binary';
const TYPE_BOOLEAN = 'boolean';
const TYPE_MONEY = 'money';
```

Миграция для таблицы, которая будет хранить данных из формы "Опрос", выглядит следующим образом (подробнее в в файле
`yii2-app-advanced/console/migrations/m150428_104828_interview.php`) :

```php
$this->createTable('{{%interview}}', [
    'id' => Schema::TYPE_PK,
    'name' => Schema::TYPE_STRING . ' NOT NULL',
    'sex' => Schema::TYPE_BOOLEAN . ' NOT NULL',
    'planets' => Schema::TYPE_STRING . ' NOT NULL',
    'astronauts' => Schema::TYPE_STRING. ' NOT NULL',
    'planet' => Schema::TYPE_INTEGER . ' NOT NULL',
], $tableOptions);
```

Обратите внимание, что для описания типов данных используется не только константы, но и ключевые слова, например `NOT NULL`.
Вы можете дополнять типы данных ключевыми словами до нужного вам состояния. 

<p class="alert alert-info">
Ключевое слова `Unsigned` не рекомендуется использовать в миграциях - 
<a href="https://github.com/yiisoft/yii2/issues/1032" target="_blank">обсуждение на GitHub</a>.
</p>

С применением миграций мы уже сталкивались, когда создавали таблицу `user`. Применим новую миграцию:

```
yii2-tutorial\yii2-app-advanced> php yii migrate
Yii Migration Tool (based on Yii v2.0.3)

Total 1 new migration to be applied:
        m150428_104828_interview

Apply the above migration? (yes|no) [no]:yes
*** applying m150428_104828_interview
    > create table {{%interview}} ... done (time: 0.048s)
*** applied m150428_104828_interview (time: 0.116s)

Migrated up successfully.
```

Таблица в базе данных создана. Теперь, чтобы использовать Active Record, необходимо создать модель, как отражение
строки из СУБД. Чтобы облегчить эту задачу, в Yii есть замечательный инструмент Gii, который генерирует код.

#### Gii - магический инструмент, который может написать код за вас.
Gii включен в Advanced шаблоне приложения, если это приложение инициализировано в режиме отладки, т.е. как было
ранее сделано, через 

```
php init --env=Development
```

Чтобы попасть в Gii, нужно перейти по ссылке <a href="/yii2-app-advanced/frontend/web/index.php?r=gii" target="_blank">
index.php?r=gii</a> и выбрать пункт **Model Generator**.
 
Если ваш сайт установлен не на локальном хосте, то скорее всего вы увидите на странице Gii ошибку доступа 403.

> Forbidden (#403) You are not allowed to access this page.

<a href="http://www.yiiframework.com/doc-2.0/yii-gii-module.html#$allowedIPs-detail" target="_blank">Доступ по умолчанию</a>
разрешён только для `['127.0.0.1', '::1'];` IP адресов. Настраивается свойство `$allowedIPs` для Gii через конфигурационные
файлы в директории `config`. Настройки доступа, в зависимости от окружения, на котором запущен сайт, могут изменяться.
Поэтому такие настройки принято хранить не в `main.php`, а в `main-local.php`. Откройте `yii2-app-advanced/frontend/config/main-local.php` и 
измените строку:

```php
$config['modules']['gii'] = 'yii\gii\Module';
```

на 

```php
$config['modules']['gii'] = [
    'class' => 'yii\gii\Module',
    'allowedIPs' => ['192.168.0.*']
];
```

Теперь на страницу <a href="/yii2-app-advanced/frontend/web/index.php?r=gii" target="_blank">index.php?r=gii</a> разрешено
будет заходить только с тех устройств, которые находятся в подсети 192.168.0.0/24

Вернёмся к Model Generator. Этот раздел Gii предназначен для генерации моделей. Для того, чтобы форма была сгенерирована, 
необходимо указать:

- имя таблицы 
- имя будущей модели `Interview`
- пространство имени `frontend\models`

остальные поля оставляем как есть.
Нажмите кнопку Preview (предпросмотр) и посмотрите `models\Interview.php` - будущий код. После этого нажмите Generate.
Всё наша модель создана и доступна по `/yii2-app-advanced/frontend/models/Interview.php`. Gii всё же не всесилен и 
потребуется внести некоторые изменения в модель. Добавим элемент "проверочный код" - `verifyCode`, как свойство модели:

```php
class Interview extends \yii\db\ActiveRecord
{  
    public $verifyCode;
    
    //...
}
```

изменим метки для будущих элементов:

```php
public function attributeLabels()
{
    return [
        'name' => 'Имя',
        'sex' => 'Пол',
        'planets' => 'Какие планеты обитаемы?',
        'astronauts' => 'Какие космонавты известны?',
        'planet' => 'На какую планету хотели бы полететь?',
        'verifyCode' => 'Проверочный код',
    ];
}
```

И так модель формы готова, сделаем саму форму. Опять обратимся за помощью к Gii, только теперь выберем генератор
`Form Generator`, в котором следует указать:

- имя вида (View Name) - `site/interview`
- имя модели с учётом пространства имён - `frontend\models\Interview`

Все остальные поля оставим как есть. Нажмём кнопку Preview, а затем Generate - создастся вид в директории 
`views/site/interview.php`. Также Gii предложит код действия для контроллера, который необходимо самостоятельно
вставить в контроллер `SiteController`. Вот чуть измененный код действия:

```php
public function actionInterview()
{
    $model = new \frontend\models\Interview();
    
    if ($model->load(Yii::$app->request->post())) {
        if ($model->validate()) {
            // делаем что-то, если форма прошла валидацию
            return;
        }
    }
    
    return $this->render('interview', [
        'model' => $model,
    ]);
}
```

Итак модель, контроллер с действием и представление созданы, теперь можно посмотреть на результат - 
<a href="/yii2-app-advanced/frontend/web/index.php?r=site/interview" target="_blank">index.php?r=site/interview</a>

<img src="/scripts/assets/screen0.2-3.jpg" class="img-responsive">

#### Настройка вида формы

Изменим вид формы на 

<img src="/scripts/assets/screen0.2-4.jpg" class="img-responsive">

- Вид элемента `name` остаётся неизменным.
- Вид элемента `sex` необходимо переделать на два переключателя. По умолчанию `$form->field()` генерирует текстовый 
`<input type="text">`. Это можно изменить следующим образом:
```php
   <?= $form->field($model, 'sex')->radioList(['Мужчина', 'Женщина']) ?>
```
<a href="http://www.yiiframework.com/doc-2.0/yii-widgets-activefield.html#radioList()-detail" target="_blank">
ActiveForm->ActiveField->radioList()</a> - метод в качестве первого элемента принимает массив возможных значений. Так как метка 
атрибута `sex` в модели определена как `'sex' => 'Пол'`, а необходимо "Вы мужчина/женщина?". То можно изменить "пол" на "вы",
что не совсем понятно, если эти метки в других местах (например в письме, отчётных таблицах или в прочем). Поэтому 
сделаем это только в виде, с помощью 
<a href="http://www.yiiframework.com/doc-2.0/yii-widgets-activefield.html#label()-detail" target="_blank">ActiveField->label()</a>. 

```php
<?= $form->field($model, 'sex')->radioList(['Мужчина', 'Женщина'])->label('Вы:') ?>
```

- Дальше идёт список флаги(checkbox). Для генерации их используем метод 
<a href="http://www.yiiframework.com/doc-2.0/yii-widgets-activefield.html#checkboxList()-detail" target="_blank">ActiveField->checkboxList()</a>. 

```php
<?= $form->field($model, 'planets')->checkboxList(
    ['Меркурий', 'Венера', 'Земля', 'Марс', 'Юпитер', 'Сатурн', 'Уран', 'Нептун']
)->label('Какие планеты по вашему мнению обитаемы?') ?>
```

- Дальше список с множественным выбором(select) и подсказкой(hint) - 
<a href="http://www.yiiframework.com/doc-2.0/yii-widgets-activefield.html#dropDownList()-detail" target="_blank">ActiveField->dropDownList()</a>:

```php
<?= $form->field($model, 'astronauts')->dropDownList(
    [
        'Юрий Гагарин',
        'Алексей Леонов',
        'Нил Армстронг',
        'Валентина Терешкова',
        'Эдвин Олдрин',
        'Анатолий Соловьев'
    ],
    ['size' => 6, 'multiple' => true]
)
->hint('С помощью Ctrl вы можете выбрать более одного космонавта')
->label('Какие космонавты вам известны?') ?>
```

<a href="http://www.yiiframework.com/doc-2.0/yii-widgets-activefield.html#hint()-detail" target="_blank">ActiveField->hint()</a> - 
формирует подсказку для элемента формы.

- Дальше выпадающий список с одиночным выбором - метод `ActiveField->dropDownList()`:

```php
<?= $form->field($model, 'planet')->dropDownList(
    ['Меркурий', 'Венера', 'Земля', 'Марс', 'Юпитер', 'Сатурн', 'Уран', 'Нептун']
) ?>
```

- И виджет каптчи, как элемент формы:

```php
<?= $form->field($model, 'verifyCode')->widget(
    yii\captcha\Captcha::className(),
    [
        'template' => '<div class="row"><div class="col-xs-3">{image}</div><div class="col-xs-4">{input}</div></div>',        
    ]
)->hint('Нажмите на картинку, чтобы обновить.') ?>
```

Html шаблона у каптчи формируется исходя из свойства `yii\captcha\Captcha::template`. Чтобы настроить само `{image}` используем
`CaptchaAction::minLength`, `CaptchaAction::maxLength`, `CaptchaAction::height` свойства, которые настраиваются в действии
`captcha`, контроллера `SiteController`.

```php
public function actions()
{
    return [            
        'captcha' => [
            'class' => 'yii\captcha\CaptchaAction',
            'minLength'=>3,
            'maxLength'=>4,
            'height'=>40,
            'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
        ],
        //...
    ];
}
```
<p class="alert alert-info">
Когда формируется элемент капчта, то для получения изображения виджет <a href="http://www.yiiframework.com/doc-2.0/yii-captcha-captcha.html" target="_blank">yii\captcha\Captcha</a>,
по умолчанию, посылает запрос на `site/captcha`. Действие <a href="http://www.yiiframework.com/doc-2.0/yii-captcha-captchaaction.html" target="_blank">yii\captcha\CaptchaAction</a> 
возвращает изображение каптчи и при этом сохраняет в сессию пользователя проверочный код, для последующей валидации.
</p>

Обновите страницу с формой. Вид у неё не такой как у результата, который мы ожидали. Всё дело в
том, что в виде `site/interview.php` Gii сгенерировал `use \yii\widgets\ActiveForm` вместо `use yii\bootstrap\ActiveForm;`.
Измените пространство имен на ` yii\bootstrap\`. Как описывалось ранее, это позволит использовать стили Bootstrap для форм.
Вид формы настроен.

#### Валидация формы

Модель `Interview` на данный момент использует правила для проверки, которые Gii подобрал на основании 
типов полей в базе данных. Перепишем в модели `frontend/models/Interview.php` некоторые правила валидации:

```php
public function rules()
{
    return [
        [['name', 'sex', 'planets', 'astronauts', 'planet', 'verifyCode'], 'required'],
        ['name', 'string'],
        ['sex', 'boolean', 'message' => 'Пол выбран не верно.'],
        [
            ['planets', 'planet'],
            'in',
            'range' => range(0, 7),
            'message' => 'Выбран не корректный список планет.',
            'allowArray' => 1
        ],
        [
            'astronauts',
            'in',
            'range' => range(0, 5),
            'message' => 'Выбран не корректный список космонавтов.',
            'allowArray' => 1
        ],
        ['verifyCode', 'captcha'],
    ];
}
```

#### Дополнительная информация для самостоятельного ознакомления:

- Ознакомьтесь с информацией о работе с формами в
<a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/start-forms.md" target="_blank">официальном
руководстве</a>.

- Ознакомьтесь с информацией о проверке данных
<a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/input-validation.md" target="_blank">официальном
руководстве</a>.

- Ознакомьтесь с информацией о генерация кода при помощи Gii
<a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/start-gii.md" target="_blank">официальном
руководстве</a>.