### Обработка формы.

В этом разделе рассмотрим как в Yii работать с базой данных, сессиями. Познакомимся с проведениями и событиями.

Чтобы начать, выполните команду из директории yii2-tutorial

```
git checkout -f step-0.3
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

- если это строка, то путь будет создан не как `http://localhost:8888/yii2-app-advanced/frontend/web/index.php?r=/site/interview`,
а как `http://localhost:8888/site/interview`.

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
use yii\helpers\Url;

class SiteController extends Controller
{
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
}
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
    if (Yii::$app->session->get('уникальный-ключ') === null) {
        $model = new Interview();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                Yii::$app->session->set('уникальный-ключ',1);
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

Т.е. можно догадаться, что эти две константы описывают методы, которые сработают до действия и после. 

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
        if (Yii::$app->session->get('уникальный-ключ') !== null) {
            echo "ДОСТУП ЗАКРЫТ";
        }
    }
);
$this->on(
    $this::EVENT_AFTER_ACTION,
    function () {
        Yii::$app->session->set('уникальный-ключ', 1);
    }
);
```

Сразу возникают вопросы:

- Как отключить EVENT_AFTER_ACTION, если данные в форме некорректные и требуют правок со стороны пользователя?
- Куда этот код вставлять?

Сейчас контроллер **ведёт** себя определённым образом:

- Контроллер последовательно вызывает метод beforeAction() приложения и самого контроллера.
Если один из методов вернул false, то остальные, невызванные методы beforeAction будут пропущены, а выполнение действия будет отменено;
По умолчанию, каждый вызов метода beforeAction() вызовет событие EVENT_BEFORE_ACTION.
- Контроллер запускает действие: параметры действия будут проанализированы и заполнены из данных запроса.
- Контроллер последовательно вызывает методы afterAction контроллера и приложения.
По умолчанию, каждый вызов метода afterAction() вызовет событие EVENT_AFTER_ACTION.

Нужно как-то вклинится в это **поведение** с проверками сессии. Для работы с поведениями в Yii используется
<a href="http://www.yiiframework.com/doc-2.0/yii-base-behavior.html" target="_blank">yii\base\Behavior</a>

<p class="alert alert-info">Рекомендуется ознакомится с <a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/concept-behaviors.md" target="_blank">
информацией о поведениях в Yii 2</a>
</p>

У контроллера есть метод `behaviors()`, в котором можно описать поведения. Сейчас в `SiteController`:

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

Что этот код обозначает мы разберём в ближайших главах, а сейчас просто добавим своё поведение, назовём его к примеру
`accessOnce`.

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
классам  Yii, то в поведении, после создания его объекта, свойству `$owner` присваивается объект, который вызвал это поведение. 
По-простому: объект класса `SiteController` является владельцем поведения `AccessOnce` и может быть в поведении получен через `$this->owner`.
Следовательно, становится доступно влиять на события владельца поведения, через `$this->owner->on(...)`. Но опять же,
куда вставлять этот код? Логичнее было бы прикрепить обработчик события при создании объекта поведения, делается это через 
переопределение метода `yii\base\Behavior::events()`:

```
class AccessOnce extends Behavior
{        
    public function events()
    {
        $owner = $this->owner;
    
        if ($owner instanceof \yii\web\Controller) {
            return [
                $owner::EVENT_BEFORE_ACTION => 'имя_обработчика',
                $owner::EVENT_AFTER_ACTION => 'имя_обработчика',
            ];
        }
        
        return parent::events();
    }
}
```

Т.к. поведение может быть прикреплено к разным объектам(контроллерам, моделям, представлениями и прочему), то событий 
EVENT_BEFORE_ACTION и EVENT_AFTER_ACTION у этих объектов может и не быть. Поэтому вводим дополнительную проверку

```php
if ($owner instanceof \yii\web\Controller) {
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
любом контроллере присутствует код:

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
                throw new \yii\web\HttpException(403, $this->message);
            }
        }
    }
}
```

При срабатывании 

```php
throw new \yii\web\HttpException(403, $this->message);
```

пользователя перекинет на `/index.php?r=site/error`. Самостоятельно попробуйте разобраться как сработает `site/error`.

После всего сделанного, в итоге имеем:

```php
class SiteController extends \yii\web\Controller
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

        if ($owner instanceof \yii\web\Controller) {
            return [
                $owner::EVENT_BEFORE_ACTION => 'checkAccess',
                $owner::EVENT_AFTER_ACTION => 'closeDoor',
            ];
        }
        
        return parent::events();
    } 
    
    //...
}
```

Первый раз EVENT_BEFORE_ACTION пускает нас на страницу, так как не обнаруживает переменную в сессии. 
EVENT_AFTER_ACTION устанавливает эту переменную и при повторном заходе на страницу,
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

Теперь обработчик EVENT_BEFORE_ACTION `checkAccess` будет срабатывать каждый раз. А обработчик `closeDoor` события
EVENT_AFTER_ACTION будет срабатывать, только когда сработает  `return $this->redirect(Url::home());`, в противном
случае поведение будет откреплено от контроллера и не сможет влиять на обработку события.

#### Осталось сохранить данные.

Таблицу `interview` в базе данных, которая описывает нашу модель `frontend/models/Interview`, мы создали ранее.
Напомним, что использовали шаблон проектирования Active Record. Модель `Interview` наследует всю функциональность
из <a href="http://www.yiiframework.com/doc-2.0/yii-db-activerecord.html" target="_blank">yii\db\ActiveRecord</a>,
поэтому трудностей с сохранением не должно возникнуть. Нужно использовать лишь один метод `save($runValidation == true)`, 
который также включает в себя валидацию данных. Т.е. метод `$model->validate()` мы можем заменить на `$model->save()` 
в контроллере `SiteController`.

```php
public function actionInterview()
{
    $model = new Interview();

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
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

Метод `save($runValidation)` подразумевает под собой следующий сценарий:

1. вызывается `beforeValidate()`, если `$runValidation = true`. Если `$runValidation = false` этот и последующий шаг
 игнорируется.
2. вызывается `afterValidate()`.
3. вызывается `beforeSave()`. Если метод возвращает false, то процесс прерывается и дальнейшие шаги не выполняются.
4. происходит сохранение данных в базу данных
5. вызывается `afterSave()`;

В нашем случае есть один нюанс - например, на вопрос "Какие космонавты вам известны?", пользователь может выбрать несколько
вариантов. Следовательно данные поступят в модель в виде массива значений, а в базе это поле хранится в виде строки.
Получается необходимо преобразовать массив данных в строку для последующего сохранения. Сделаем это с помощью 
переопределения метода `beforeSave()` в модели Interview:

```php
public function beforeSave($insert)
{
    if (parent::beforeSave($insert)) {
        $this->planets = implode(',', $this->planets);
        $this->astronauts = implode(',', $this->astronauts);
        return true;
    }

    return false;
}
```

#### Дополнительная информация для самостоятельного ознакомления:

- <a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/structure-models.md" target="_blank">Модели</a>.
- <a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/start-databases.md" target="_blank">Работа с базами данных</a>.
- <a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/concept-components.md" target="_blank">Компоненты</a>.
- <a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/runtime-sessions-cookies.md" target="_blank">Сессии и куки</a>.
- <a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/runtime-routing.md" target="_blank">Работа с URL</a>.

