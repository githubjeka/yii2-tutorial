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
пользователя статус-ответ о обработки запроса в "обратную связь" и отправляется 

```php
return $this->refresh();
```

ответ с заголовками, которые обновляют текущую страницу.

### Создание формы.

Теперь давайте создадим новую форму.

<p class="alert alert-info">Ознакомьтесь с информацией о работе с формами в
<a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/start-forms.md" target="_blank">официальном
руководстве</a>.
</p>
