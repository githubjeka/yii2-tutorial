### Обработка формы.

В этом разделе рассмотрим как в Yii2 работать с базой данных, сессиями. Познакомимся с проведениями и событиями.

Чтобы начать, выполните команду из директории yii2-tutorial

```
git checkout -f step-1
```

В предыдущих главах вы уже узнали: что такое контроллер, что такое модель, что такое виды с шаблонами, как
это всё взаимосвязано и где располагается. Поэтому далее многая информация будет дана без разъяснений этих основ.

Вспомните, создавая форму "Опрос", контроллер `SiteController` ничего в ответ не посылал, когда данные от
пользователя поступали валидные:

```php
if ($model->load(Yii::$app->request->post())) {
    if ($model->validate()) {
        // делаем что-то, если форма прошла валидацию
        return;
    }
}
```

Логичнее было отправить сообщение пользователю о успешном принятии его ответов. Также необходимо сохранить эти ответы,
для последующего анализа. Ещё запретим принимать участие в опросе тем пользователям, кто уже ответил на вопросы.

#### Компонент для работы с сессией.

Давайте сначала проинформируем пользователя об успешном принятии его ответов и перенаправим его на домашнюю страницу,
после того как он ввёл корректные данные. Для информирования пользователя запишем ему сообщение в сессию. Когда пользователя 
перебросит на домашнюю страницу, покажем сообщение из сессии и удалим его впоследствии. 

<p class="alert alert-info">
Ознакомьтесь с информацией <a href="http://php.net/manual/ru/book.session.php" target="_blank">"Управление сессиями в PHP"</a>
</p>

В Yii2 для работы с сессиями используется компонент <a href="http://www.yiiframework.com/doc-2.0/yii-web-session.html" target="_blank">yii\web\Session</a>,
к которому можно обратиться через `\Yii::$app->session`. У компонента Session есть свойство `$flash`, которое предназначено
именно для нашей задачи. Следующий код

```php
Yii::$app->session->setFlash(
    'success',
    'Спасибо, что уделили время. В ближайшее время будут опубликованы результаты.'
);
```

создаст в сессии пользователя сообщение с ключом `success`. Останется только вывести это сообщение. Yii упрощает это до 
предельно возможной простоты - ничего не нужно делать. В главном шаблоне `main.php` есть код:

```php
<?= \frontend\widgets\Alert::widget() ?>
```

это виджет, который располагается в директории `yii2-app-advanced\frontend\widgets\`. Откройте его и ознакомьтесь.
 
Alert виджет выводит сообщение из сессии, которое было задано с помощью `setFlash` и распознаёт по ключу, какой стиль css Bootstrap
применить к данном сообщению. В данном случае ключ `success` подключит css `'alert alert-success'`:

<p class="alert alert-success">
Спасибо, что уделили время. В ближайшее время будут опубликованы результаты.
</p>

И так добавим `yii\web\Session->setFlash()` в действие контроллера:

```php
public function actionInterview()
{
    $model = new Interview();
    if ($model->load(Yii::$app->request->post())) {
        if ($model->validate()) {
            Yii::$app->session->setFlash(
                'success',
                'Спасибо, что уделили время. В ближайшее время будут опубликованы результаты.'
            );                      
        }
    }    
    return $this->render('interview', ['model' => $model,]);
}
```

Теперь когда форма запроса будет верно заполнена и отправлена, должно появиться сообщение. 
<a href="/yii2-app-advanced/frontend/web/index.php?r=site/interview" target="_blank">Проверьте.</a>
Работает? Уверены? А вдруг сломается? Чтобы всегда быть уверенным добавим проверку в наш ранее созданный тест формы.
Вставляем в то место, где отправили корректные данные:

```php
//...
$I->expectTo('не увидеть ошибки валидации'); //expectTo - ожидаю
$I->see('Спасибо, что уделили время. В ближайшее время будут опубликованы результаты.');
//...
```

Сформируем исполнителей тестов

```
codecept build
```

и запустим тест

```
codecept run functional functional\InterviewCept.php

OK (1 test, 16 assertions)
```

Все команды запускаем из `yii2-app-advanced/tests/codeception/frontend`. Теперь не нужно, при каждом изменении кода, 
заполнять нашу форму и смотреть вывелось ли сообщение пользователю. Достаточно в будущем запустить тест.

После успешной отправки формы, страница с формой опять выводится на экран, так как сработал `$this->render('interview'...)`.
Перенаправим пользователя, на домашнюю страницу. Для формирования URL адресов в Yii используется класс помощник
<a href="http://www.yiiframework.com/doc-2.0/yii-helpers-url.html" target="_blank">yii\helpers\Url</a>. В API этого класса
можно найти метод `home()`, который перенаправляет на домашнюю страницу. Домашняя страница может быть задана через конфигурацию
приложения `\Yii::$app->homeUrl`, так же, как настраивали язык и имя нашего приложения:

```php
return [
    'name' => 'Мой сайт',
    'language' => 'ru',
    'homeUrl' => ['/site/interview'],
]
```

Url формируется как id контроллера и id действия - это не строка, а массив так как:
- вторым и последующим элементом может быть переданы $_GET параметры
 
```php
'homeUrl' => ['/site/page', 'view'=>'duty'],
```

- если это строка, то путь будет создан не как `.../web/index.php?r=/site/interview`, а как `http://localhost:9000/site/interview`

Имя приложения и язык приложения мы настраивали `yii2-app-advanced/common/config/main.php`. Эта конфигурация располагается
в директории `common`, что означает что конфигурация будет применена ко всем приложениями - консольному, административной 
части (backend), клиентской части (frontend) и другим. Мы работаем в `frontend`, поэтому homeUrl установим только для 
него, так как, например, в административной части URL домашней страницы может и не быть.

В файле конфигурации `yii2-app-advanced/frontend/config/main.php` добавьте код:

```php
'homeUrl' => ['/site/page', 'view'=>'about'],
```

Теперь при вызове `Url::homeUrl()` будет сформирован `/index.php?r=site/page&view=about`. Чтобы перенаправить пользователя
по этому адресу используем метод контроллера `redirect()`

```php
public function actionInterview()
{
    $model = new Interview();
    if ($model->load(Yii::$app->request->post())) {
        if ($model->validate()) {
            Yii::$app->session->setFlash(
                'success',
                'Спасибо, что уделили время. В ближайшее время будут опубликованы результаты.'
            );
            return $this->redirect(Url::home());
        }
    }    
    return $this->render('interview', ['model' => $model,]);
}
```

Запустите ещё раз тест, чтобы проверить, что сообщение всё ещё выводится:

```
codecept run functional functional/InterviewCept.php

OK (1 test, 16 assertions)
```

Теперь когда пользователь ответит на опрос, его перебросит на домашнюю страницу. Но он может схитрить - снова вернуться
на страницу с формой и ответить заново. Ограничим доступ к форме, если пользователь уже отвечал. Сделаем это через 
всю туже сессию - когда пользователь открывать страницу с формой, в контроллере будет срабатывать проверка по поиску
определённого ключа в сессии, если он найден, то запретим пользователю дальнейшую работу с формой. Ключ в сессии будем
создавать только, когда форма прошла валидацию.

Такое ограничение нам может понадобиться в будущем в разных ситуациях: участие в акциях, опросах, вручение подарков и 
прочее. Поэтому давайте сделаем так, чтобы можно было легко использовать наш код в разных действиях контроллеров. Т.е. 
если писать:

```php
public function actionInterview()
{
    if (Yii::$app->get('уникальный-ключ') === null) {
        $model = new Interview();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                Yii::$app->set('уникальный-ключ',1);
                return $this->redirect(Url::home());
            }
        }
        return $this->render('interview', ['model' => $model,]);
    } else {
        echo "ДОСТУП ЗАКРЫТ";
    }
}
```

то нужно будет в других действиях проделывать аналогичные действия. В
<a href="http://www.yiiframework.com/doc-2.0/yii-base-controller.html" target="_blank">yii\base\Controller</a> есть 
константы:

```php
const EVENT_BEFORE_ACTION = 'beforeAction';
const EVENT_AFTER_ACTION = 'afterAction';
```

Из их названия видно, что первое называется "СОБЫТИЕ_ПОСЛЕ_ДЕЙСТВИЯ", второе - "СОБЫТИЕ_ПЕРЕД_ДЕЙСТВИЕМ". 

#### События и поведения.

> Событие — то, что происходит в некоторый момент времени и рассматривается как изменение состояния чего-либо. 

Т.е. можно догадаться, что эти две константы описывают методы, которые сработают до нашего действия и после. 

<p class="alert alert-info">Рекомендуется ознакомится с <a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/concept-events.md" target="_blank">
информацией о событиях в Yii 2</a>
</p>

При срабатывании события EVENT_BEFORE_ACTION, нам необходимо проверить, есть ли ключ в сессии пользователя.
А при срабатывании EVENT_AFTER_ACTION нам необходимо установить этот ключ, но с одной оговоркой - если форма корректна.

В контроллере нужно написать, что-то вроде такого:

```php
$this->on(
    $this::EVENT_BEFORE_ACTION,
    function () {
        if (Yii::$app->get('уникальный-ключ') !== null) {
            echo "ДОСТУП ЗАКРЫТ";
        }
    }
);
$this->on(
    $this::EVENT_AFTER_ACTION,
    function () {
        Yii::$app->set('уникальный-ключ', 1);
    }
);
```

Сразу возникают вопросы:

- Как отключить EVENT_AFTER_ACTION, если данные в форме некорректные и требуют правок со стороны пользователя?
- Куда этот код вставлять?

Сейчас контроллер **ведёт** себя определённым образом:

- Контроллер последовательно вызывает метод beforeAction() приложения и самого контроллера.
Если один из методов вернул false, то остальные, невызванные методы beforeAction будут пропущены, а выполнение действия будет отменено;
По-умолчанию, каждый вызов метода beforeAction() вызовет событие EVENT_BEFORE_ACTION.
- Контроллер запускает действие: параметры действия будут проанализированы и заполнены из данных запроса.
- Контроллер последовательно вызывает методы afterAction контроллера и приложения.
По-умолчанию, каждый вызов метода afterAction() вызовет событие EVENT_AFTER_ACTION.

Нужно как-то вклинится в это **поведение** с проверками сессии. Для работы с поведениями в Yii 2 используется
<a href="http://www.yiiframework.com/doc-2.0/yii-base-behavior.html" target="_blank">yii\base\Behavior</a>

<p class="alert alert-info">Рекомендуется ознакомится с <a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/concept-behaviors.md" target="_blank">
информацией о поведениях в Yii 2</a>
</p>

У контроллера есть метод `behaviors()`, в котором можно описать свои поведения. Сейчас в `SiteController`:

```php
public function behaviors()
{
    return [           
        'access' => [
            'class' => AccessControl::className(),
            'only' => ['logout', 'signup'],
            'rules' => [
                [
                    'actions' => ['signup'],
                    'allow' => true,
                    'roles' => ['?'],
                ],
                [
                    'actions' => ['logout'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ],
        'verbs' => [
            'class' => VerbFilter::className(),
            'actions' => [
                'logout' => ['post'],
            ],
        ],
    ];
}
```

Что этот код обозначает мы разберём в ближайших главах, а сейчас просто добавим своё поведение.

```php
public function behaviors()
{
    return [
        'accessOnce' => [
            'class' => 
        ],        
        //...
    ];
}
```

Нужно указать класс поведения - создадим его. Создайте директорию `yii2-app-advanced/frontend/behaviors` и в ней класс:

```php
<?php
namespace frontend\behaviors;

use yii\base\Behavior;

class AccessOnce extends Behavior
{

}
```

допишем в контроллере:

```php
'accessOnce' => [
    'class' => \frontend\behaviors\AccessOnce::className(),
],
```

В принципе это "пустышка", т.е. если перейти на <a href="/yii2-app-advanced/frontend/web/index.php?r=site/interview" target="_blank">
страницу с формой "Опроса"</a>, то ничего изменится. Но когда поведение прикреплено к наследнику базового класса 
<a href="http://www.yiiframework.com/doc-2.0/yii-base-object.html" target="_blank">yii\base\Object</a>, т.е. к почти ко всем
классам  Yii2, то в поведении, после создания его объекта, свойству `$owner` присваивается объект, который вызвал это поведение. 
По-простому: объект класса `SiteController` является владельцем поведения `AccessOnce` и может быть в поведении получен через `$this->owner`.
Следовательно, становится доступно влиять на события владельца поведения, через `$this->owner-on(...)`. Но опять же,
куда вставлять этот код. Логичнее было бы прикрепить обработчик события при создании объекта поведения, делается это через 
переопределение метода `yii\base\Behavior::events()`:

```
class AccessOnce extends Behavior
{        
    public function events()
    {
        $owner = $this->owner;
    
        if ($owner instanceof Controller) {
            return [
                $owner::EVENT_BEFORE_ACTION => 'имя_обработчика',
                $owner::EVENT_AFTER_ACTION => 'имя_обработчика',
            ];
        }
    }
}
```

Т.к. поведение может быть прикреплено к разным объектам(контроллерам, моделям, представлениями и прочему), то событий 
EVENT_BEFORE_ACTION и EVENT_AFTER_ACTION у этих объектов может и не быть. Поэтому вводим дополнительную проверку

```php
if ($owner instanceof Controller) {
```

которая ограничит неверное использование поведения AccessOnce.

Теперь создадим обработчиков, которые будут срабатывать при наступлении событий:

```php
public function имя_обработчика($event)
{
    
}
```

В обработчике будет доступно, $event - наследник класса <a href="http://www.yiiframework.com/doc-2.0/yii-base-event.html" target="_blank">yii\base\Event</a>
Наследник определяется в зависимости от того, кто это событие вызвал. В данном случае $event -
<a href="http://www.yiiframework.com/doc-2.0/yii-base-actionevent.html" target="_blank">yii\base\ActionEvent</a>, т.к. в 
любом контроллере:

```php
public function beforeAction($action)
{
    $event = new ActionEvent($action);
    $this->trigger(self::EVENT_BEFORE_ACTION, $event);
    return $event->isValid;
}

public function afterAction($action, $result)
{
    $event = new ActionEvent($action);
    $event->result = $result;
    $this->trigger(self::EVENT_AFTER_ACTION, $event);
    return $event->result;
}
```

И так создадим обработчик, который закрывает доступ, создаёт переменную в сессии.

```php
public function closeDoor(\yii\base\ActionEvent $event)
{
    if ($event->action->id === 'interview') {
        \Yii::$app->session->set('interview-access-lock', 1);
    }
}
```

Но как же универсальность, а если мы захотим прикрепить наше поведение на другое действие, не `interview`? Переделаем.
Добавим в наше поведение переменную `$actions`, которое будет следить на какие действия вешать "замок".
 
```php
class AccessOnce extends Behavior
{
    public $actions = [];    
    
    public function closeDoor(\yii\base\ActionEvent $event)
    {
        if (in_array($event->action->id, $this->actions, true)) {
            \Yii::$app->session->set($event->action->id . '-access-lock', 1);
        }
    }
}
```

Аналогично сделаем обработчик, который будет проверять переменную в сессии

```php
class AccessOnce extends Behavior
{
    public $actions = [];
        
    public $message = 'Доступ ограничен. Вы ранее совершали действия на этой странице.';  
    
    public function checkAccess(\yii\base\ActionEvent $event)
    {
        if (in_array($event->action->id, $this->actions, true)) {
            if (\Yii::$app->session->get($event->action->id . '-access-lock') !== null) {
                throw new HttpException(403, $this->message);
            }
        }
    }
}
```

При срабатывании 

```php
throw new HttpException(403, $this->message);
```

пользователя перекинет на `/index.php?r=site/error`. Самостоятельно попробуйте разобраться как сработает `site/error`.

После всего сделанного, в итоге имеем:

```php
class SiteController extends Controller
{  
    public function behaviors()
    {
        return [
            'accessOnce' => [
                'class' => '\frontend\behaviors\AccessOnce',
                'actions' => ['interview']
            ],
        ];
    }
    
     //...
}


class AccessOnce extends Behavior
{   
    public function events()
    {
        $owner = $this->owner;

        if ($owner instanceof Controller) {
            return [
                $owner::EVENT_BEFORE_ACTION => 'checkAccess',
                $owner::EVENT_AFTER_ACTION => 'closeDoor',
            ];
        }
    } 
    
    //...
}
```

Теперь, если запустить наш тест

```
codecept run functional functional/InterviewCept.php

Tests: 1, Assertions: 1, Failures: 1
```

то будет пройдена только одна проверка. Происходит это потому, что после отправки некорректных данных 

```
$interviewPage->submit([]);
```

получается открытие второй раз `$interviewPage`. Первый раз EVENT_BEFORE_ACTION пускает нас на страницу, так как не 
обнаруживает переменную в сессии. EVENT_AFTER_ACTION устанавливает эту переменную и при повторном заходе на страницу,
EVENT_BEFORE_ACTION нас уже не пропускает. Чтобы изменить такое поведение, нужно отключить EVENT_AFTER_ACTION,
если данные не корректные. Для этого отключаем поведение, через `Controller::detachBehaviors`:

```php
public function actionInterview()
{
    $model = new Interview();
    if ($model->load(Yii::$app->request->post())) {
        if ($model->validate()) {
            Yii::$app->session->setFlash(
                'success',
                'Спасибо, что уделили время. В ближайшее время будут опубликованы результаты.'
            );
            return $this->redirect(Url::home());
        }
    }
    $this->detachBehaviors('accessOnce');
    return $this->render('interview', ['model' => $model,]);
}
```

Обработчик EVENT_BEFORE_ACTION `checkAccess` будет срабатывать каждый раз. А обработчик `closeDoor` события
EVENT_AFTER_ACTION будет срабатывать, только когда сработает  `return $this->redirect(Url::home());`

Запустим тест:

```
codecept run functional functional/InterviewCept.php

Tests: 1, Assertions: 15, Failures: 1
```

Одна ошибка возникает в последней проверке:
```
$I->amGoingTo('отправить форму c некорректным проверочным кодом'); //amGoingTo - собираюсь
```

Так как до этой проверки, мы отправили корректные данные, то второй раз на страницу с формой нас не пускает. Переделаем
чуть тест, участок кода с 

```php
$I->amGoingTo('отправить форму c некорректным проверочным кодом'); //amGoingTo - собираюсь
$interviewPage = InterviewPage::openBy($I);
$interviewPage->submit([
    'verifyCode' => 'wrongText',
]);

$I->expectTo('увидеть ошибки валидации каптчи'); //expectTo - ожидаю
$I->see('Неправильный проверочный код.', '.help-block');
```

перенесём чуть выше проверки:

```
$I->amGoingTo('отправить форму c корректными данными'); //amGoingTo - собираюсь
```

А в конце теста добавим проверку, которая будет определять, что доступ ограничен.

```
$I->amGoingTo('открыть форму второй раз'); //amGoingTo - собираюсь
$interviewPage = InterviewPage::openBy($I);
$I->see('Доступ ограничен. Вы ранее совершали действия на этой странице.');
```

Запускаем тест:

```
codecept run functional functional/InterviewCept.php

OK (1 test, 17 assertions)
```

#### Осталось сохранить данные.

Для хранения будем использовать базу данных SQLite, ранее созданную. Напомним, что для обращения к базе данных используется компонент, который мы настроили в 
`yii2-app-advanced/common/config/main-local.php` конфигурации приложения:

```php
'db' => [
    'class' => 'yii\db\Connection',
    'dsn' => 'sqlite:' . dirname(__FILE__) .'/../../sqlite.db',
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
в Yii реализован <a href="http://www.yiiframework.com/doc-2.0/yii-db-schema.html" targer="_blank">абстрактный класс yii\db\Schema</a>.
Этот класс описывает схему, как хранится информация в СУБД. При создании запроса определяется на основании `dns` компонента 
`yii\db\Connection`, какую схему нужно использовать. В свою очередь эта схема реализует работу с данными в зависимости от СУБД.

Миграция для таблицы, которая будет храненить данных из формы "Опрос", выглядит следующим образом(Подробнее в в файле
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

И также для тестов:

```
yii2-tutorial\yii2-app-advanced\tests\codeception\bin> php yii migrate
```

Таблица `interview`, которая описывает нашу модель "Опрос", в основной и тестовой базе данных создана.

Для сохранения данных в таблицу  понадобятся методы `yii\db\Connection::createCommand` и `yii\db\Command::execute()`. Первый 
создаёт sql запрос и возвращает объект `yii\db\Command`, второй - отправляет запрос на исполнение:

```php
Yii::$app->db->createCommand('запрос')->execute()
```

Можно сохранить данные после их успешной валидации:

```php
if ($model->load(Yii::$app->request->post())) {
    if ($model->validate()) {
        Yii::$app->db->createCommand('запрос')->execute()       
    }
}
```

Но по канонам MVC в контроллере не принято работать с базой данных.

> Контроллер подвергает проверке и контролю вводные данные от пользователя и использует модель и представление для 
реализации необходимой реакции.

Создадим метод в модели `Interview`, который сохранит данные в базу данных. Затем к этому методу обратимся из контроллера.

```php
public function save(array $attributes)
{
    if ($this->validate()) {

        $values = $this->getAttributes($attributes);

        foreach ($values as &$value) {
            if (is_array($value)) {
                $value = implode(' ', $value);
            }
        }

        $attributesAsString = implode(', ', $attributes);
        $values = array_map(
            function ($v) {
                return '"' . $v . '"';
            },
            $values
        );
        $values = implode(', ', $values);

        return \Yii::$app->db->createCommand(
            "INSERT INTO interview ($attributesAsString) VALUES ($values)"
        )->execute();
    }

    return false;
}
```

Перед сохраненнием, потребуется провести валидацию данных, поэтому в метод save включена `$this->validate($attributes)`.
Поля `planets` и `astronauts` были заданы как `Schema::TYPE_STRING`, а от пользователя эти поля приходят как массив 
значений. С помощью php функции `implode` переделаем данные из массива в строку, как требует того 
<a href="https://www.sqlite.org/lang_insert.html" target="_blank">SQLite insert</a>. Ну и выполним запрос через

```php
Yii::$app->db->createCommand('запрос')->execute();
```

Теперь воспользуемся этотим методом в `SiteController`:

```php
public function actionInterview()
{
    $model = new Interview();
    if ($model->load(Yii::$app->request->post()) && $model->save(['name', 'sex', 'planets', 'astronauts', 'planet'])) {
            Yii::$app->session->setFlash(
                'success',
                'Спасибо, что уделили время. В ближайшее время будут опубликованы результаты.'
            );
            return $this->redirect(Url::home());           
    }

    $this->detachBehaviors('accessOnce');

    return $this->render('interview', ['model' => $model,]);
}
```

Теперь данные пользователя будут сохраняться в БД. Проверим это с помощью теста. Так как необходимо проверить метод 
`save()` внезависимости от условий, то будем использовать unit тест, вместо functional. 

> Цель Unit тестов - изолировать отдельные части кода и показать, что по отдельности эти части работоспособны.

Создадим в `yii2-app-advanced/tests/codeception/frontend/unit/models` файл `InterviewTest.php`

```php
<?php
namespace tests\codeception\frontend\unit\models;

use tests\codeception\frontend\unit\DbTestCase;
use Codeception\Specify;

class InterviewTest extends DbTestCase
{
    use Specify;

    public function testSaveInterview()
    {
        
    }    
}
```

Понадобится `tests\codeception\frontend\unit\DbTestCase` и `Codeception\Specify`. Первый помогает работать с фикстурами 
`Fixtures`. В functional тестах мы описывали страницу `InterviewPage.php` на которую заходили, а в unit нужно описать
среду в которой будет использоваться наш тест. Т.е. в данном случае необходимо описание таблицы `Interview` в базе данных.

> Автоматические тесты необходимо выполнять неоднократно. Мы хотели бы выполнять тесты в некоторых известных состояниях 
для гарантии повторяемости процесса тестирования. Эти состояния называются фикстуры. Например, для тестирования функции 
создания записи в приложении, каждый раз, когда мы выполняем тесты, таблицы, хранящие соответствующие данные о 
записях, должны быть восстановлены к некоторому фиксированому состоянию.

Трейт `Codeception\Specify` позволяет писать тесты в BDD стиле. 

<p class="alert alert-info">
<a href="http://en.wikipedia.org/wiki/Behavior-driven_development" target="_blank">Wiki Behavior Driven Development (BDD)</a>
</p>
<p class="alert alert-info">
<a href="http://www.ibm.com/developerworks/ru/library/j-cq09187/" target="_blank">Знакомство с Behavior Driven Development (BDD)</a>
</p>
<p class="alert alert-info">
<a href="https://github.com/Codeception/Specify" target="_blank">Codeception/Specify on Github</a>
</p>

Создадим фикстуру `yii2-app-advanced/tests/codeception/frontend/unit/fixtures/InterviewFixture.php`:

```php
<?php
namespace tests\codeception\frontend\unit\fixtures;
use yii\test\ActiveFixture;
class InterviewFixture extends ActiveFixture
{
    public $tableName = 'interview';
}
```

и подключим её в unit тесте `InterviewTest.php` через метод `fixtures()`:

```php
public function fixtures()
{
    return [
        'interview' => [
            'class' => 'tests\codeception\frontend\unit\fixtures\InterviewFixture',
        ],
    ];
}
```

Всё готово опишем тест `testSaveInterview()`:

```php
public function testSaveInterview()
{
    $model = new \frontend\models\Interview(
        [
            'name' => 'Ivanov',
            'sex' => 1,
            'planets' => [1, 2, 3],
            'astronauts' => [2, 3],
            'planet' => 5,
            'verifyCode' => 'testme',
        ]
    );

    $model->save(['name', 'sex', 'planets', 'astronauts', 'planet']);

    $modelFromDb = \Yii::$app->db->createCommand('SELECT * FROM interview WHERE name="Ivanov"')->queryOne();

    $this->specify(
        'Ответы должны быть отправлены',
        function () use ($modelFromDb) {
            expect('имя должно быть сохранено верно', $modelFromDb['name'])->equals('Ivanov');
            expect('пол должен быть сохранен верно', $modelFromDb['sex'])->equals('1');
            expect('планеты должен быть сохранены верно', $modelFromDb['planets'])->equals('1,2,3');
            expect('космонавты должен быть сохранены верно', $modelFromDb['astronauts'])->equals('2,3');
            expect('планета должен быть сохранена верно', $modelFromDb['planet'])->equals(5);
        }
    );
}
```

В тесте первым делом создаём модель формы и через <a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/concept-configurations.md" target="_blank">
конфигурацию</a> заполняем её данными и сохраняем её с помощью метода `save()`, который проверяем. Затем с помощью
`\yii\db\Command->queryOne()` извлекаем из базы данных одну запись и переходим к проверкам `specify()`.

Когда всё готово запускаем тест из `...tests/codeception/frontend`:

```php
codecept run unit /unit/models/InterviewTest.php

OK (1 test, 5 assertions)
```

Каждый раз при запуске этого теста, с помощью фикстур состояние таблицы `$tableName = 'interview'` будет сброшено, т.е.
все данные, которые в ней хранятся будут удалены. Позже мы рассмотрим как применить фикстуру, для сброса таблицы,
но при этом заполнить её определёнными данными. 

А пока проверим наш предыдущий функциональный тест, чтобы убедиться, что ничего не поломалось:

```php
codecept run functional /functional/InterviewCept.php

OK (1 test, 17 assertions)
```

Теперь каждый раз при внесении изменений в код, нам нет необходимости запускать браузер, заполнять форму выдуманными данными и 
проверять работает ли та или иная функциональность. Теперь вы сможете проделать всё это за считанные секунды.

Напоследок вернёмся к методу `save()`. В нём мы использовали `array_map` и `implode`, так как подстраивались под 
требования SQLlite, что не совсем правильно. Ведь можно использовать MySQL или другую базу данных, которая 
будет требовать совершенно другого синтаксиса команды INSERT. Yii приходит на помощь. Для вставки данных можно 
воспользоваться методом `insert()` из <a href="http://www.yiiframework.com/doc-2.0/yii-db-command.html" target="_blank">yii\db\Command</a>
, который подберёт нужный синтаксис для вставки данных, а также обезопасит от 
<a href="https://ru.wikipedia.org/wiki/%D0%92%D0%BD%D0%B5%D0%B4%D1%80%D0%B5%D0%BD%D0%B8%D0%B5_SQL-%D0%BA%D0%BE%D0%B4%D0%B0">
SQL инъекции</a>. Ведь в `$values` может быть чем угодно.

```php
"INSERT INTO interview ($attributesAsString) VALUES ($values)"
```

Метод `save()` стоит переписать:

```php
public function save(array $attributes)
{
    if ($this->validate()) {
        $values = $this->getAttributes($attributes);
        foreach ($values as &$value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
        }
        return \Yii::$app->db->createCommand()->insert('interview', $values)->execute();
    }
    return false;
}
```

`implode` пришлось оставить, чтобы значения, которые поступили в виде массива были преобразованы в строки. Но всё же код
получился лаконичнее и безопаснее.

Запустите самостоятельно тесты, чтобы убедиться, что всё работает также.

#### Дополнительная информация для самостоятельного ознакомления:

- Ознакомьтесь более подробно с возможностями <a href="http://codeception.com/" target="_blank">Codeception</a>.
- Освежите знания о <a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/concept-components.md" target="_blank">компонетам Yii</a>.
