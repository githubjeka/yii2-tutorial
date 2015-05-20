### Формы для сохранения данных

Вы наверное уже обратили внимание на формы для сохранения:

- <a href="/yii2-app-advanced/backend/web/index.php?r=star/create" target="_blank">звёзд</a>
- <a href="/yii2-app-advanced/backend/web/index.php?r=planet/create" target="_blank">планет</a>
- <a href="/yii2-app-advanced/backend/web/index.php?r=satellite/create" target="_blank">спутников</a>

Сейчас они выглядит, мягко говоря, не удобно для того, чтобы ими пользоваться.

Давайте начнём с первой формы - сохранение информации по звёздам. Чтобы найти файл формы - смотрим на url `star/create`,
далее открываем контроллер `StarController`, ищем в нём метод `actionCreate()`. Видим, что вызывается вид `create`. 
Следовательно открываем `yii2-app-advanced/backend/views/star/create.php` и обнаруживаем, что в этом файле нет нам уже 
знакомого класса `ActiveForm` для работы с формами. А есть:

```php
$this->render('_form', ['model' => $model,])
```

Как уже известно в видах $this - это <a href="http://www.yiiframework.com/doc-2.0/yii-web-view.html" target="_blank">yii\web\View</a>.
Метод `render`, вам встречался в контроллере, но там его реализация отличается тем, что до вывода вида, вызывается компонент
для работы с видами, т.е. вызывается `yii\web\View` и только затем его метод для получения вида из файла. Тут же `$this`
это уже компонент для работы с видами и его `render()` получает вид `_form`. Получается, что один вид `_form` находится внутри
другого вида `create`. Почему это так? Это удобно, так как вид `_form` содержит форму, которая может быть использована не
только для создания модели, но и также для изменения уже существующей модели. Если открыть вид `update.php` в этой же директории
то, можно обнаружить тот же код, что в и `create.php`:

```php
$this->render('_form', ['model' => $model,])
```

Т.е. одна форма используется для видов `create.php` и `update.php`. 

Открыв `yii2-app-advanced/backend/views/star/_form.php` вы не обнаружите ничего нового. Обычная форма - одно поле и кнопка.
В браузере эта форма занимает почти всю ширину экрана. Не совсем красиво. У 
<a href="http://www.yiiframework.com/doc-2.0/yii-bootstrap-activeform.html" target="_blank">ActiveForm</a>, что из пространства имён
`yii\bootstrap\` есть свойство `$layout`, которое может иметь значение `['default', 'horizontal', 'inline']`. Попробуйте
установить `inline`. В `views/star/_form` :

```php
<?php $form = ActiveForm::begin(['layout'=>'inline']); ?>
```

Не забудьте изменить `yii\widgets\ActiveForm;` на корректное пространство. Обновите страницу с формой и посмотрите 
на результат. Красивее, чем было, хотя о вкусах не спорят. При `inline` можно заметить, что метки (label) не используются.
Но есть <a href="http://htmlbook.ru/html/input/placeholder" target="_blank">placeholder</a>. Добавим его к `textInput`:

```php
<?= $form->field($model, 'name')->textInput(['maxlength' => 255, 'placeholder'=>'Введите название звезды']) ?>
```

С Yii версии 2.0.3 `maxlength` - максимальная длина введённого текста, может быть автоматически высчитана из правила валидации:

```php
public function rules()
{
    return [
        [['name'], 'required'],
        [['name'], 'string', 'max' => 255]
    ];
}
```

Для этого используйте:
 
```php
<?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder'=>'Введите название звезды']) ?>
```

Теперь наша форма готова. Введите название для звезды и нажав кнопку создать, почувствовать себя властелином Вселенной.
<img src="/scripts/assets/screen1.2-3.jpg" class="img-responsive">

Открыв `\backend\controllers\StarController::actionCreate` вы увидите уже знакомый принцип сохранения данных - проверка 
и дальнейшее их сохранение. Со звездой всё просто. Перейдём к <a href="/yii2-app-advanced/backend/web/index.php?r=planet/create" target="_blank">планетам</a>. 

На форме с планетами появляется новое поле - `star_id`. Тот, кто будет пользоваться этой формой, будет вспоминать программиста
не добрым словом. Всех id не упомнишь, да и ошибиться всегда можно. Давайте сделаем выпадающий список с названиями звёзд.

Как мы делали когда-то для планет:

```php
<?= $form->field($model, 'planet')->dropDownList(
    ['Меркурий', 'Венера', 'Земля', 'Марс', 'Юпитер', 'Сатурн', 'Уран', 'Нептун']
) ?>
```

Только вместо массива будем использовать запрос `Star::find()->all()`, который вернёт массив моделей.

```php
$stars = [];

foreach (Star::find()->all() as $star){
    $stars[$star->id] = $star->name;
}

echo $form->field($model, 'planet')->dropDownList($stars);
```

Но можно переписать этот код с использованием класса помощника <a href="http://www.yiiframework.com/doc-2.0/yii-helpers-arrayhelper.html" target="_blank">
yii\helpers\ArrayHelper</a>, который позволяет обращаться с массивами более эффективно:

```php
$stars = ArrayHelper::map(Star::find()->all(), 'id', 'name');
echo $form->field($model, 'planet')->dropDownList($stars);
```

Конечно, вы можете обойтись без переменной `$stars`, записав этот код одну строку. Ну и после всего, для этой формы 
попробуйте использовать `horizontal`:

```php
<?php $form = ActiveForm::begin(['layout' => 'horizontal',]); ?>
```

<img src="/scripts/assets/screen1.2-4.jpg" class="img-responsive">

Осталось <a href="/yii2-app-advanced/backend/web/index.php?r=satellite/create" target="_blank">форма создания спутников</a>.
Вы уже всё умеете, чтобы внести изменения самостоятельно.

<img src="/scripts/assets/screen1.2-5.jpg" class="img-responsive">

Так как формы, для редактирования существующих моделей используются одни и те же, что и для сохранения. То, что-либо новое
создавать не нужно.

#### Сохранение реляционных данных.

У нас есть три формы для трёх разных моделей. Представьте себе ситуацию: нужно ввести информацию по новой планете, но звезды
у неё ещё нету. Не совсем удобно переключаться с формы на форму, сохраняя новые данные. Давайте объединим работу с тремя
формами в одной. Для этого нам понадобится новая модель формы, которая будет объединять работу с тремя моделями 
относительно модели Планет.

Давайте начнём с теста. Это упростит отладку и разработку. Будем двигаться небольшими шагами, чтобы было понятнее и легче.

Создадим функциональный тест `PlanetFormCept`:

```
cd yii2-app-advanced\tests\codeception\backend\
codecept build
```

```
codecept generate:cept functional PlanetFormCept
    Test was created in PlanetFormCept.php
```

Откройте файл теста `PlanetFormCept.php` и измените его содержимое на:

```php
<?php use tests\codeception\backend\FunctionalTester;
/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
$I->wantTo('ensure than create form works');
```

Можно запустить этот тест:

```
codecept run functional functional/PlanetFormCept.php

    Time: 1.51 seconds, Memory: 13.25Mb    
    OK (1 test, 0 assertions)
```

Теперь к тесту добавим команду на открытие страницы с формой. Для этого нужно создать объект этой страницы. 
В директории `yii2-app-advanced/tests/codeception/backend/_pages/` создайте `PlanetFormPage.php`:

```php
<?php
namespace tests\codeception\backend\_pages;

use yii\codeception\BasePage;

class PlanetFormPage extends BasePage
{
    public $route = 'planet/create';
}
```

Теперь в нашем тесте можно воспользоваться этим объектом, для того, чтобы имитировать открытие страницы с формой:
  
```
//...
$I->wantTo('ensure than create form works');
$formPage = \tests\codeception\backend\_pages\PlanetFormPage::openBy($I);
```

Запускаем тест для того, чтобы убедиться, что всё выполнили правильно:

```
codecept run functional functional/PlanetFormCept.php

    Time: 518 ms, Memory: 18.00Mb  
    OK (1 test, 0 assertions)
```

На данный момент форма содержит два поля: тестовое поле "Название планеты" и выпадающий список "Название звезды".
Проверим их через тест. Для этого нам понадобятся методы `fillField` и `click` из класса 
`yii2-app-advanced/tests/codeception/backend/functional/FunctionalTester.php`, который создался с помощью ранее выполненной
команды `codecept build`.

<p class="alert alert-info">
Ознакомьтесь с информацией <a href="http://codeception.com/docs/modules/Yii2" target="_blank">по доступным методам</a>
модуля Yii2 для Codeception.
</p>

```php
<?php use tests\codeception\backend\FunctionalTester;
/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
$I->wantTo('ensure than create form works');
$formPage = \tests\codeception\backend\PlanetFormPage::openBy($I);

$I->fillField('//*[@id="planet-name"]','Земля');
$I->selectOption('//*[@id="planet-star_id"]', 'Солнце');
$I->click('//*[@id="w0"]/div[3]/button');
$I->dontSeeInTitle('Новая планета');
```

- fillField - заполняем текстовое поле "Название планеты"
- click - нажимаем на кнопку "Создать"
- dontSeeInTitle - проверяем, чтобы в заголовке странице не было текста "Новая планета"

`//*[@id="planet-name"]` и `'//*[@id="w0"]/div[3]/button'` - это <a href="https://ru.wikipedia.org/wiki/XPath" target="_blank">XPath</a>.
Например, в браузере Chrome, нажав на странице с формой F12, можно получить XPath через контекстное меню к html коду элемента:

<img src="/scripts/assets/screen1.2-6.jpg" class="img-responsive">

Запустив тест, увидим ошибку:

```
codecept run functional functional/PlanetFormCept.php
     
     InvalidArgumentException: Input "Planet[star_id]" cannot take "Солнце" as a value (possible values: ).
        3. I select option "//*[@id="planet-star_id"]","Солнце"
        //...
```

Всё потому, что используется тестовая база данных, которая никакой информации по звёздам не содержит. Данные есть только
в главной базе данных, но её использовать не будем, во избежание порчи данных. На помощь приходят фикстуры. Это состояние
базы данных, до которого она будет доведена при запуске теста. Для работы с фикстурами исполнитель функциональных тестов
`FunctionalTester.php` использует класс помощник `FixtureHelper.php` (в файле `tests/codeception/backend/functional.suite.yml`):

```
class_name: FunctionalTester
modules:
    enabled:
      - Filesystem
      - Yii2
      - tests\codeception\common\_support\FixtureHelper
    config:
        Yii2:
            configFile: '../config/backend/functional.php'
```

откройте этот файл и найдите его метод:

```php
public function fixtures()
{
    return [
        'user' => [
            'class' => UserFixture::className(),
            'dataFile' => '@tests/codeception/common/fixtures/data/init_login.php',
        ],
    ];
}
```

Запуская каждый раз любой функциональный тест из backend, стартует фикстура UserFixture:

```php
class UserFixture extends ActiveFixture
{
    public $modelClass = 'common\models\User';
}
```

, которая очищает таблицу для модели `common\models\User`. А затем заполняет её данными из `dataFile`.

Для кого-то, на первый раз это всё может показаться сложным и избыточным. Ведь мы ушли в сторону, а могли бы за это время
уже создать необходимую форму и двигаться дальше. Но нет, топчемся на месте. Тяжело в учении - легко в бою.
Сделайте паузу, выпейте кофе, расслабитесь. Когда отдохнули - продолжим. 

Создадим новые фикстуры, которые будут сбрасывать состояние таблицы для звёзд, планет и их спутников. Создайте в 
`yii2-app-advanced/tests/codeception/common/fixtures` файлы:

- PlanetFixture.php
- SatelliteFixture.php
- StarFixture.php

Вот пример одной из фикстуры `SatelliteFixture.php`:

```php
<?php
namespace tests\codeception\common\fixtures;

use yii\test\ActiveFixture;

class SatelliteFixture extends ActiveFixture
{
    public $modelClass = 'common\models\Satellite';
}

```

Остальные две: `PlanetFixture` и `StarFixture` создайте самостоятельно.



#### Дополнительная информация для самостоятельного ознакомления:

- <a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/helper-array.md" target="_blank">Руководство по ArrayHelper</a>.