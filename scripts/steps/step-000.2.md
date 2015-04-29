### Работа с формами

В этом разделе рассмотрим как создать форму, как обрабатывать данные из формы.

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
   
На первых порах возникает вопрос - зачем использовать этот виджет и вообще php код, если можно использовать HTML. Во-первых
использование виджета ускоряет процесс создания рутинных, обычных форм и делает легким процесс проверки пользовательских данных.
 
В `contact.php`

```
<?php $form = ActiveForm::begin(['id' => 'contact-form', 'enableClientValidation' => 'false']); ?>
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
шаблон проектирования приложения. Вы уже знакомы с контроллерами и представлениям, так вот $model - это недостающее звено, модель.
$model - это объект, модель которая нужна для описание сущности. В данном случае сущность - это "деловое предложение" 
или "вопрос" от пользователя, т.е. "обратная связь". В Yii для работы с моделью реализован класс 
<a href="http://www.yiiframework.com/doc-2.0/yii-base-model.html" target="_blank">`yii\base\Model`</a>. Объект модели
создаётся в контроллере и передаётся в представление

```php
public function actionContact()
{
    $model = new ContactForm();
   
    return $this->render('contact', ['model' => $model,]);    
}
```

Откройте класс `ContactForm`, он находится в `yii2-app-advanced/frontend/models/`. Вообще принято все модели располагать
в директории `application/models/`, но вы всегда можете расположить их где вам угодно с учётом <a href="http://www.php-fig.org/psr/psr-4/ru/" target="_blank">
стандарта PSR-4</a>. И так `ContactForm` имеет:

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
повторяется до контроллера. Теперь в контроллере опять создаётся модель, но перед отправкой её в представление,
проверяются переданные данные: 

```php
public function actionContact()
{
    $model = new ContactForm();
    if ($model->load(Yii::$app->request->post()) && $model->validate()) 
```

Метод `$model->load` наполняет модель пользовательскими данными, которые получаются с помощью компонента request
`Yii::$app->request->post()`. В первом уроке мы уже знакомились с одним из компонентном `db`, который служил для настройки
базы данных. Любой компонент приложения может быть вызван как `Yii::$app->имя_компонента`. Где `Yii::$app` - это приложение, 
которое было создано в входном файле `index.php`, а имя компонента можно установить через конфигурацию приложения.
Компонент `request` (служит для работы с HTTP запросами 
<a href="http://www.yiiframework.com/doc-2.0/yii-web-request.html" target="_blank">yii\web\Request</a>),
с помощью метода `post` возвращает `$_POST` данные, которые поступают
в метод `$model->load`. Этот метод модели соотносит её атрибуты с данными из формы, по принципу 

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

После `load` следует метод `$model->validate()`, который запускает вторую часть проверки данных. Метод `validate` формирует
из результат метода `rules()` различные проверки. Делает он это по следующему принципу:

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
 
#### Пользовательские данные не корректные.
Если какая-нибудь проверка прошла не успешно, то атрибут модели модель `$errors` наполняется сообщениями об ошибках 
с учётом атрибута, в котором возникла ошибка. В результате в контроллере условие не выполняется: 

```php
if ($model->load(Yii::$app->request->post()) && $model->validate()) {
```

и в вид отправляется модель с сообщениями об ошибках. В виде `contract.php` с помощью `ActiveForm::field` выводятся
элементы формы: для тех у кого ошибки, формируются сообщения об ошибках, остальные выводятся со значениями.

<img src="/scripts/assets/screen0.2-1.jpg" class="img-responsive">

По-умолчанию в виджете `ActiveForm` включено свойство `$enableClientValidation`, которое означает, что проверки выполняются
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
с помощью компонента Yii::$app->session (отвечает за сессию $_SESSION пользователя, 
<a href="http://www.yiiframework.com/doc-2.0/yii-web-session.html" target="_blank">yii\web\Session</a> формируется для 
пользователя статус-ответ о обработке запроса в "обратную связь" и отправляется 

```php
return $this->refresh();
```

ответ с заголовками, которые обновляют текущую страницу.

### Создание формы.

Построим новую форму - опрос. Сначала нужно описать модель, с которой предстоит работать.
Сформируйте для этого новый файл `Interview.php` в директории `yii2-app-advanced/frontend/models`.

```php
<?php
namespace frontend\models;

use yii\base\Model;

/**
 * Class Interview
 * Модель, которая описывает форму "Опрос"
 *
 *
 */
class Interview extends Model
{

}
```

Опишем  наши элементы формы:

- Ф.И.О.
- Пол
- Какие планеты солнечной системы обитаемы?
- Какие космонавты известны?
- На какую планету хотели бы полететь?
- Проверочный код

```php
class Interview extends Model
{
    public $name;
    public $sex;
    public $planets;
    public $astronauts;
    public $planet;
    public $verifyCode;


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
}
```

#### Gii - магический инструмент, который может написать код за вас.
Теперь нам необходимо создать контроллер и вид. Чтобы облегчить эту задачу, в Yii есть замечательный инструмент Gii,
который генерирует код. Gii включен в Advanced шаблоне приложения, если это приложение инициализировано в режиме отладки,
т.е. как было ранее сделано, через 

```
php init --env=Development
```

Позже мы познакомимся, как создавать и использовать различные режимы работы приложения. А пока вернёмся к Gii.

Чтобы попасть в Gii нужно перейти по ссылке <a href="/yii2-app-advanced/frontend/web/index.php?r=gii" target="_blank">
index.php?r=gii</a> и выбрать пункт **Form Generator**. Form Generator предназначен для генерации кода форм. Для того, 
чтобы форма была сгенерирована, необходимо указать:

- имя вида (View Name) - `site/interview`
- имя модели с учётом пространства имён - `frontend\models\Interview`

Все остальные поля оставим как есть. Нажмём кнопку Preview (предпросмотр) и посмотрим `views\interview.php` будущий код:

```php
<?php

use yii\helpers\Html; 
use yii\widgets\ActiveForm; 

/* @var $this yii\web\View */ 
/* @var $model frontend\models\Interview */ 
/* @var $form ActiveForm */ 
?> 
<div class="interview"> 

    <?php $form = ActiveForm::begin(); ?> 

     
        <div class="form-group"> 
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?> 
        </div> 
    <?php ActiveForm::end(); ?> 

</div><!-- interview --> 
```

Можно обратить внимание, что будут сгенерированы теги открытия и закрытия формы и кнопка для отправки формы. 

А как же остальные элементы формы? Gii перед генерацией просматривает, какие атрибуты у модели являются безопасными, те и выводит.
Так как мы не указывали правила валидации в нашей модели, то Gii посчитал все атрибуты небезопасными. Исправим это, добавив
в модель примитивную валидацию - все поля обязательны для заполнения:

```php
public function rules()
{
    return [
        [['name', 'sex', 'planets', 'astronauts', 'planet', 'verifyCode'], 'required']
    ];
}
```

Теперь ещё раз в Gii нажмём Preview и увидим новые элементы формы. Теперь можно нажимать Generate - создастся вид в 
директории `views/site/interview.php` и на экране Gii предложит код действия для контроллера, который необходимо самостоятельно
вставить в нужный контроллер. Вот чуть измененный код действия:

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

Вставьте код этого действия в контроллер `SiteController`.

Итак модель, контроллер с действием и представление созданы, теперь можно посмотреть на результат - 
<a href="/yii2-app-advanced/frontend/web/index.php?r=site/interview" target="_blank">index.php?r=site/interview</a>

<img src="/scripts/assets/screen0.2-3.jpg" class="img-responsive">

#### Настройка вида формы

Изменим вид формы на 

<img src="/scripts/assets/screen0.2-4.jpg" class="img-responsive">

- Вид элемента `name` остаётся неизменным.
- Вид элемента `sex` необходимо переделать на два переключателя. По-умолчанию `$form->field()` генерирует текстовый 
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
по-умолчанию, посылает запрос на `site/captcha`. Действие <a href="http://www.yiiframework.com/doc-2.0/yii-captcha-captchaaction.html" target="_blank">yii\captcha\CaptchaAction</a> 
возвращает изображение каптчи и при этом сохраняет в сессию пользователя проверочный код, для последующей валидации.
</p>

Обновите страницу с формой. Вид у неё не такой как у результата, который мы ожидали. Всё дело в
том, что в виде `site/interview.php` Gii сгенерировал `use \yii\widgets\ActiveForm` вместо `use yii\bootstrap\ActiveForm;`.
Измените пространство имен на ` yii\bootstrap\`. Как описывалось ранее, это позволит использовать стили Bootstrap для форм.
Вид формы настроен.

#### Валидация формы

Модель `Interview` на данный момент использует одно правило для проверки:

```php
  [['name', 'sex', 'planets', 'astronauts', 'planet', 'verifyCode'], 'required']
```

Перед тем как добавить дополнительные проверки, создадим проверочный тест формы. В Yii2 для тестирования кода используется
<a href="http://codeception.com/" target="_blank">codeception</a>. Чтобы установить codeception нужно в любой директории
создать файл `composer.json` c содержимым:

```json
{
    "require": {
        "codeception/codeception": "*",
        "codeception/verify": "*",
        "codeception/specify": "*"
    }
}
```

и запустить из той же директории `composer install`. После автоматической установки всех зависимостей, можно запускать codeception.
Располагается входной файл в `ваша_директория\vendor\bin\`. Но всё же лучше настроить переменную 
<a href="https://ru.wikipedia.org/wiki/PATH_%28%D0%BF%D0%B5%D1%80%D0%B5%D0%BC%D0%B5%D0%BD%D0%BD%D0%B0%D1%8F%29" targer="_blank">PATH</a>
на эту директории, чтобы команда `codecept` была доступна из любого места.

В Yii2 Advanced всё, что нужно для работы с тестами, располагается в директории `yii2-tutorial\yii2-app-advanced\tests`.

Первоначальная настройка тестового окружения сводится к:

- Инициализации "действующий лиц"(исполнителей тестов) codecept, через команду%

```
yii2-tutorial\yii2-app-advanced\tests\codeception\frontend> codecept build
```

Выполните её.

- и к настройке тестовой базы данных (в этом уроке настройка базы данных уже произведена).
В `yii2-tutorial\yii2-app-advanced\tests\codeception\config\config.php` меняем настройки компонента `db` на:

```php
'dsn' => 'sqlite:' . dirname(__FILE__) .'/../../sqlite-test.db',
```

И запускаем миграции для тестовой базы (вот ещё одно применение миграций):

```
yii2-tutorial/yii2-app-advanced/tests/codeception/bin> php yii migrate 
```
 
Тестовая база данных нужна для того, чтобы не испортить данные на основной. Например при некоторых тестах, таблицы 
могут быть очищены и заполнены новыми тестовыми данными.

Можно попробовать запустить тесты, которые содержит Advanced шаблон. Для этого выполним:

```
yii2-tutorial\yii2-app-advanced\tests\codeception\frontend> codecept run unit

//...

OK (8 tests, 20 assertions)
```

8 тестов с 20 проверками выполнены успешно. `run unit` обозначает запуск юнит-тестирования.

> Цель Unit тестов - изолировать отдельные части кода и показать, что по отдельности эти части работоспособны.
 
Добавим свой тест для формы "Опрос". Будем использовать функциональное тестирование, так как нам нужно проверить всю форму, 
а не отдельный её части.

> Функциональное тестирование — это тестирование ПО в целях проверки реализуемости функциональных требований, то есть
 способности ПО в определённых условиях решать задачи, нужные пользователям. 
 
Создайте в `/yii2-app-advanced/tests/codeception/frontend/functional/` файл `InterviewCept.php`:

```php
<?php
use tests\codeception\frontend\FunctionalTester;

/* @var $scenario Codeception\Scenario */

$I = new FunctionalTester($scenario);
```

На человеческий язык, этот код выглядит как - "Я тестировщик функционала". `FunctionalTester` - это и есть одно из "действующий
лиц", которые создались при первоначальной настройке Codeception, выполняя `codecept build`. Итак, у нас есть объект 
`$I`(Я). Создадим такой тест:

- Я хочу открыть страницу с формой "опроса".
- Я хочу быть уверенным, что форма "опроса" открывается и работает.
- Я хочу видеть ошибки при отправке пустой формы.
- Я не хочу видеть ошибки, когда форма заполнена и отправлена.

Переводим тест с человеческого языка на `codeception`. Сперва нужно описать, что такое "страница с формой опроса". 
Создаём файл `InterviewPage.php` в `tests/codeception/frontend/_pages` c следующим содержимым:

```php
<?php
namespace tests\codeception\frontend\_pages;

use \yii\codeception\BasePage;

/**
 * Описывает страницу формы "Опрос" 
 */
class InterviewPage extends BasePage
{
    public $route = 'site/interview';    
}
```

После этого в `InterviewCept.php` дописываем:

```php
<?php
use tests\codeception\frontend\_pages\InterviewPage;
use tests\codeception\frontend\FunctionalTester;

/* @var $scenario Codeception\Scenario */

$I = new FunctionalTester($scenario);
$I->wantTo('быть уверенным, что страница с формой "опрос" работает.'); //wantTo - хочу
$interviewPage = InterviewPage::openBy($I); // открываем страницу 'site/interview'
$I->amGoingTo('отправить форму без данных'); //amGoingTo - собираюсь
```

Тут понадобится метод, который заполнит форму и отравит её. Создадим его в `InterviewPage`:

```php
public function submit(array $formData)
{
    foreach ($formData as $field => $value) {
        if ($field === 'name' || $field === 'verifyCode') {
            $this->actor->fillField('input[name="Interview[' . $field . ']"]', $value);
        } elseif ($field === 'planets') {
            foreach ($value as $val) {
                $this->actor->checkOption('input[name="Interview[' . $field . '][]"][value=' . $val . ']');
            }
        } else {
            $this->actor->selectOption('[name="Interview[' . $field . ']"]', $value);
        }
    }

    $this->actor->click('interview-submit');
}
```

где `$this->actor` - это `FunctionalTester`. В метод нужно передать массив значений формы `$formData`, в виде `attribute=>value`.
Actor по attribute вычисляет, как заполнить то или иное поле. Input заполнятся через `fillField()`, checkbox через `checkOption()`,
select и radio - `selectOption()`. Когда всё заполнено, тестировщик нажимает на кнопку отравить.`$this->actor->click('interview-submit');`.
На данный момент кнопки с `name="interview-submit"` в виде `frontend/views/site/interview.php` нет.  Поэтому следует добавить к кнопке 
`name`:

```php
 <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'name' => 'interview-submit']) ?>
```

Дополняем тест проверками:

```
//...
$I->amGoingTo('отправить форму без данных'); //amGoingTo - собираюсь

$interviewPage->submit([]);

$I->expectTo('увидеть ошибки валидации'); //expectTo - ожидаю
$I->see('Необходимо заполнить «Имя».', '.help-block'); //see - вижу
$I->see('Необходимо заполнить «Пол».', '.help-block');
$I->see('Необходимо заполнить «Какие планеты обитаемы?».', '.help-block');
$I->see('Необходимо заполнить «Какие космонавты известны?».', '.help-block');
$I->see('Необходимо заполнить «Проверочный код».', '.help-block');

$I->amGoingTo('отправить форму c корректными данными'); //amGoingTo - собираюсь
$interviewPage->submit([
    'name' => 'Иванов',
    'sex' => '1',
    'planets' => [1,2,3],
    'astronauts' => [1,2,3],
    'planet' => 1,
    'verifyCode' => 'tes0tme',
]);

$I->expectTo('не увидеть ошибки валидации'); //expectTo - ожидаю
$I->dontSee('Необходимо заполнить «Имя».', '.help-block');
$I->dontSee('Необходимо заполнить «Пол».', '.help-block');
$I->dontSee('Необходимо заполнить «Какие планеты обитаемы?».', '.help-block');
$I->dontSee('Необходимо заполнить «Какие космонавты известны?».', '.help-block');
$I->dontSee('Необходимо заполнить «Проверочный код».', '.help-block');
```

`'verifyCode' => 'testme',` - задан именно так потому, что `SiteController::captchaAction` фиксирует
значение для каптчи, если приложение запущено в режиме тестов:

```php
'captcha' => [
    'class' => 'yii\captcha\CaptchaAction',
    'minLength'=>3,
    'maxLength'=>4,
    'height'=>40,
    'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
],
```

Сейчас можно запустить наш тест и убедиться, что всё верно.
В `yii2-tutorial\yii2-app-advanced\tests\codeception\frontend`:

```
codecept run functional functional/InterviewCept.php

OK (1 test, 10 assertions)
```

Добавим в модели `frontend/models/Interview.php` несколько правил валидации, которые будут следить за тем, что 
посланные данные корректные.

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

Список встроенных валидаторов можно посмотреть в 
<a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/tutorial-core-validators.md" target="_blank">
официальном руководстве</a>

Добавим к нашему тесту пару проверок:

```php
//...

$I->dontSee('Пол выбран не верно.', '.help-block');
$I->dontSee('Выбран не корректный список планет.', '.help-block');
$I->dontSee('Выбран не корректный список космонавтов.', '.help-block');
$I->dontSee('Неправильный проверочный код.', '.help-block');

$I->amGoingTo('отправить форму c некорректным проверочным кодом'); //amGoingTo - собираюсь
$interviewPage = InterviewPage::openBy($I);
$interviewPage->submit([
    'verifyCode' => 'wrongText',
]);

$I->expectTo('увидеть ошибки валидации каптчи'); //expectTo - ожидаю
$I->see('Неправильный проверочный код.', '.help-block');
```

```
codecept run functional functional/InterviewCept.php

OK (1 test, 15 assertions)
```

Перед отправкой некорректного проверочного кода, мы ещё раз открыли страницу `InterviewPage::openBy`, так как до этого
мы отправляли корректные данные в контроллер `SiteController`:

```php
if ($model->validate()) {
    // делаем что-то, если форма прошла валидацию
    return;
}
```

Т.е. контроллер вернул пустой ответ. И мы бы никаких элементов больше не нашли, поэтому и перегрузили страницу.

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