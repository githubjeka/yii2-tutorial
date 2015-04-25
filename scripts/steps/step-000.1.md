### Виды и шаблоны

В этом разделе рассмотрим как создать новую страницу со статическим текстом.

Чтобы начать, выполните команду из директории yii2-tutorial

```
git checkout -f step-0.1
```

Перейдите <a href="http://localhost:9000/yii2-app-advanced/frontend/web/index.php?r=site%2Fabout" target="_blank">
по ссылке</a> вы попадёте на статическую страницу "About".

Если посмотреть на адрес ссылки, то можно увидеть `index.php?r=site%2Fabout`.
`index.php` это входной скрипт нашего приложения. Именно через него идут все запросы пользователя на исполнение.
Дальше связка `site%2Fabout (эквивалентно site/about)`. `site` - имя контроллера, который обрабатывает наш запрос, 
`about` - действие, в контроллере которое мы вызываем. Т.е., внутри, Yii переделывает `site` в класс `SiteController`, 
а `about` в метод `function actionAbout() {...}` и вызывает его на исполнение.

Найдём этот контроллер и этот метод. Контроллер лежит в `yii2-app-advanced/frontend/controllers/SiteController.php`, 
по-умолчанию все контроллеры принято располагать в папке `controllers/` c суффиксом `Controller` 
(<a href="https://github.com/yiisoft/yii2/issues/2709" target="_blank">Почему так?</a>).

<p class="alert alert-info">Подробнее о контроллерах и действиях в 
<a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/structure-controllers.md" target="_blank">официальном
руководстве</a>
</p>

В контроллере `SiteController` сейчас вызов статической страницы реализован, через `actionAbout()`:

```php
public function actionAbout()
{
    return $this->render('about');
}
```

Метод возвращает статический текст, который состоит из шаблона и вида.

> Виды - это часть MVC архитектуры, это код, который отвечает за представление данных конечным пользователям.

> Шаблоны - особый тип видов, которые представляют собой общие части разных видов.

<p class="alert alert-info">Подробнее о видах и шаблонах в 
<a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/structure-views.md" target="_blank">официальном
руководстве</a>
</p>

В данном случае у нас `'about'` - это вид, файл который лежит в директории `yii2-app-advanced/frontend/views/site/`.

```php
<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Это статическая страница, которая может быть изменена в файле:</p>

    <code><?= __FILE__ ?></code>
</div>
```

Из содержимого файла `about.php` можно понять, что доступен объект `$this` - 
<a href="http://www.yiiframework.com/doc-2.0/yii-web-view.html" target="_blank">yii\web\View</a>.
Этот объект достен во всех видах и шаблонах. В данном случае у нас используется его свойство, `$this->title`, которое
отвечает за заголовок открытой страницы. Также этот заголовок передаётся в "Навигационную цепочку", через 

```php
$this->params['breadcrumbs'][] = $this->title;
```

Попробуйте поменять заголовок "About" на текст "О нас!" и откройте 
<a href="/yii2-app-advanced/frontend/web/index.php?r=site/about" target="_blank">страницу</a>.

<img src="/scripts/assets/screen0.1.jpg" class="img-responsive">

Видно, что в меню, всё ещё осталось "About". Чтобы это исправить, нужно внести правки в код этого меню. В роли меню 
выступает виджет <a href="http://www.yiiframework.com/doc-2.0/yii-bootstrap-nav.html" target="_blank">yii\bootstrap\Nav</a>.

> Виджеты представляют собой многоразовые строительные блоки, используемые в видах для создания элементов пользовательского интерфейса.

<p class="alert alert-info">Подробнее о виджетах в
<a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/structure-widgets.md" target="_blank">официальном
руководстве</a>
</p>

Виджет меню подключается в шаблоне, который подключается перед показом вида `about`. Чтобы определить какой шаблон 
используется, то нужно обратиться к текущему контроллеру ``SiteController` и его методу `render()`, которое мы вызываем
в нашем действии `actionAbout()`. Метод `render()` обращается к свойству `layout` текущего контроллера, для определения шаблона.
Если это свойство не задано у контроллера, то ищется шаблон экземпляра приложения. В данном случае в роли экземпляра
приложения выступает класс `yii\web\Application`, который создаётся при запуске приложения.

<p class="alert alert-info">Подробнее о процессе "Запуск приложения" в
<a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/start-workflow.md" target="_blank">официальном
руководстве</a>
</p>

В общем, обычно всё находится в директории `yii2-app-advanced/frontend/views/layouts` в файле `main.php`. Иногда разработчики
приложения изменяют эту ситуацию, настраивая конфигурацию `yii\web\Application` или `SiteController` на своё усмотрение.
Yii никого в этом не ограничивает. В данный момент, ограничимся тем, что имеем по-умолчанию.

Итак, откройте `yii2-app-advanced/frontend/views/layout/main.php` и ознакомьтесь с содержимым. Это шаблон, который
подключается к каждому виду. Т.е. по сути это главная HTML разметка для всех страниц приложения. Виджет меню имеет вид

```php
use yii\bootstrap\Nav;

NavBar::begin([
//...
NavBar::end();
```

Найдите код внутри этого виджета

```php
['label' => 'About', 'url' => ['/site/about']],
```

И просто изменить `label` на 

```php
['label' => 'О нас', 'url' => ['/site/about']],
```

Можете самостоятельно попробовать изменить все остальные пункты меню.
<img src="/scripts/assets/screen0.1-1.jpg" class="img-responsive">

Как видно, всё ещё остались "My Company" и "Home". Возможно вы уже поменяли "My Company" на свой текст, просто заменив его.
У приложения есть <a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/structure-applications.md#%D0%A1%D0%B2%D0%BE%D0%B9%D1%81%D1%82%D0%B2%D0%B0-%D0%BF%D1%80%D0%B8%D0%BB%D0%BE%D0%B6%D0%B5%D0%BD%D0%B8%D0%B9-" target="_blank">
свойства</a>, которые доступны для конфигурации. А именно "имя приложения". Оно как раз подходит для того, чтобы быть задействованным 
в данном случае. Настроим его.

Для этого нужно обратиться к настройкам приложения. Мы это уже делали в уроке, когда знакомились с шаблоном приложения 
Advanced. Только на этот раз, это будет файл не `yii2-app-advanced/common/config/main-local.php`, а `main.php` в той же 
директории. И измените его на

```php
<?php
return [
    'name' => 'Мой сайт',
    'language' => 'ru',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
];
```

Изменив `'language' => 'ru',`, изменился основной язык приложения. Доступные языки для приложения можно обнаружить в 
`yii2-app-advanced/vendor/yiisoft/yii2/messages/`. Все возможные сообщения, которые были на английском, станут на русском.
<img src="/scripts/assets/screen0.1-2.jpg" class="img-responsive">

Свойство `'name' => 'Мой сайт',` доступно в качестве конструкции `Yii::$app->name`. Не сложно догадаться, что 
``Yii::$app->language` вернёт `ru`. Т.е. к любому <a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/structure-applications.md#%D0%A1%D0%B2%D0%BE%D0%B9%D1%81%D1%82%D0%B2%D0%B0-%D0%BF%D1%80%D0%B8%D0%BB%D0%BE%D0%B6%D0%B5%D0%BD%D0%B8%D0%B9-" target="_blank">
свойству</a> приложения можно обратиться именно так.

Изменим в главном шаблоне "My Company" на `Yii::$app->name`

```php
  NavBar::begin([
                'brandLabel' => Yii::$app->name,
```

и чуть ниже в footer:

```html
<p class="pull-left">&copy; <?= Yii::$app->name ?> <?= date('Y') ?></p>
```

### Хочу ещё больше статических страниц!
Когда у вас будет много статический страниц "О нас", "Режим работы", "Доставка" и прочее, то не совсем удобно, каждый раз
в контроллере создавать метод:
 
```php
class SiteController extends Controller
{
    public function actionAbout()
    {
        return $this->render('about');
    }
    
    public function actionDuty()
    {
        return $this->render('duty');
    }
    
    public function actionDelivery()
    {
        return $this->render('delivery');
    }
}
```

Для этого уже реализовано одно действие для контроллеров. Это `yii\web\ViewAction`

<p class="alert alert-info">Рекомендуется ознокомится с <a href="http://www.yiiframework.com/doc-2.0/yii-web-viewaction.html" target="_blank">
API класса ViewAction</a> и перечитать про <a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/structure-controllers.md#%D0%9E%D1%82%D0%B4%D0%B5%D0%BB%D1%8C%D0%BD%D1%8B%D0%B5-%D0%B4%D0%B5%D0%B9%D1%81%D1%82%D0%B2%D0%B8%D1%8F-" target="_blank">
отдельные действия</a> в контроллерах.
</p>

Найдите в `SiteController` метод `actions`, в котором уже имеется:

```php
'page' => [
    'class' => 'yii\web\ViewAction',
],
```

По-умолчанию в Advanced этого кода нет, он добавлен для вашего удобства.
Теперь нужно перейти по адресу 
<a href="/yii2-app-advanced/frontend/web/index.php?r=site/page&view=about" target="_blank">
index.php?r=site/page&view=about
</a>

Попробуйте поменять в адресной строке параметр `view` на <a href="/yii2-app-advanced/frontend/web/index.php?r=site/page&view=duty" target="_blank">
duty</a> или <a href="/yii2-app-advanced/frontend/web/index.php?r=site/page&view=delivery" target="_blank">
delivery</a>. Проанализируйте результаты.

