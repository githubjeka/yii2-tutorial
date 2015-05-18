### Работа с реляционными данными

Вы уже познакомились с тем как с помощью Active Record можно получить запись из базы данных. В этом разделе
научимся получать связанные данные и работать с ними.

Чтобы начать, выполните команду из директории yii2-tutorial:

```
git checkout -f step-1.1
```

Сперва давайте на словах определимся, что мы хотим получить. Возьмём нашу солнечную систему. В нашей солнечной системе
есть звезда Солнце, вокруг звезды вращаются планеты - Меркурий, Венера, Земля, Марс, Церера, Юпитер, Сатурн, Уран, 
Нептун, Плутон, Хаумеа, Макемаке, Эрида; а вокруг планет их спутники. Для хранение этих данных нам понадобятся три 
таблицы: звезды, планеты, спутники.

Star

```
| id | name |
|----|------|
|    |      |
```

Planet

```
| id | name | star_id |
|----|------|---------|
|    |      |         |
```

Satellite

```
| id | name | planet_id |
|----|------|-----------|
|    |      |           |
```


<p class="alert alert-info">Хорошим тоном служит именовать таблицы в единственном числе на английском языке, например 
Planet, но не Planets. Внешние ключи принято называть в сочетании имени и поля таблицы, например "planet_id", а первичные
ключи - "id". <a href="https://toster.ru/q/139295" target="_blank">Подробнее...</a>
</p>

Давайте создадим эти таблицы, через миграцию. Выполните в `yii2-tutorial\yii2-app-advanced`:

```
php yii migrate/create create_asto_tables

Yii Migration Tool (based on Yii v2.0.3)
Create new migration '/yii2-tutorial/yii2-app-advanced/console/migrations/m150513_054155_create_asto_tables.php'? (yes|no) [no]:yes
New migration created successfully.
```

Приведём код миграции к следующему виду:

```php
<?php

use yii\db\Schema;
use yii\db\Migration;

class m150513_054155_create_asto_tables extends Migration
{
    public function up()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {         
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%star}}',
            [
                'id' => Schema::TYPE_PK,
                'name' => Schema::TYPE_STRING . ' NOT NULL',
            ],
            $tableOptions
        );

        $this->createTable(
            '{{%planet}}',
            [
                'id' => Schema::TYPE_PK,
                'name' => Schema::TYPE_STRING . ' NOT NULL',
                'star_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'FOREIGN KEY(star_id) REFERENCES '
                . $this->db->quoteTableName('{{%star}}') . '(id)'
            ],
            $tableOptions
        );

        $this->createTable(
            '{{%satellite}}',
            [
                'id' => Schema::TYPE_PK,
                'name' => Schema::TYPE_STRING . ' NOT NULL',
                'planet_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'FOREIGN KEY(planet_id) REFERENCES '
                . $this->db->quoteTableName('{{%planet}}') . '(id)'
            ],
            $tableOptions
        );
    }

    public function down()
    {
        $this->dropTable('{{%satellite}}');
        $this->dropTable('{{%planet}}');
        $this->dropTable('{{%star}}');
    }
}

```

Обратите внимание, что имена таблиц имеют вид `"{{%name}}"`. Это необходимо, если вы захотите использовать префикс в 
именах таблиц, который можно установить через конфигурацию компонента `db`:

```php
 'db' => [
    'class' => 'yii\db\Connection',
    'dsn' => 'sqlite:' . __DIR__ . '/../../sqlite.db',
    'tablePrefix' => 'astro',
],
```

Так как наша миграция, может в будущем использоваться не только на SQLite, но и на Mysql, то для Mysql с помощью
`$tableOptions` устанавливаем кодировку и `ENGINE=InnoDB`, для работы с внешними ключами `FOREIGN KEY`. В SQLite по-умолчанию 
<a href="https://www.sqlite.org/foreignkeys.html#fk_enable" target="_blank">проверка внешних ключей отключена</a>.
Для того, чтобы её включить необходимо выполнить команду:

```
PRAGMA foreign_keys = ON;
```

Выполнять её требуется всякий раз, когда устанавливается соединение. У `yii\db\Connection` есть события:

- `EVENT_AFTER_OPEN` - срабатывает каждый раз, после установки соединения с БД.
- `EVENT_BEGIN_TRANSACTION` - срабатывает каждый раз, перед началом транзакции.
- `EVENT_COMMIT_TRANSACTION` - срабатывает каждый раз, после применении транзакции. 
- `EVENT_ROLLBACK_TRANSACTION` - срабатывает каждый раз, после отмены транзакции.

Присоединим на событие `EVENT_AFTER_OPEN` функцию-обработчик, которая будет включать проверку внешних ключей в SQLite.
Это можно сделать, через глобальную конфигурацию компонента:

```php
'db' => [
    'class' => 'yii\db\Connection',
    'dsn' => 'sqlite:' . __DIR__ . '/../../sqlite.db',   
    'on afterOpen' => function ($event) {
        $event->sender->createCommand('PRAGMA foreign_keys = ON;')->execute();
    }
],
```

Заметьте, что таким способом (`'on имя_события'=>обработчик`) можно присоединять обработчики к любым событиям 
компонентов или приложений. Так же можно можно поступить и с поведениями. Например, запретить доступ "гостям" к 
методу `logout` в контроллере `site`, можно с помощью `as access`

```php
'as access' => [
    'class' => 'yii\filters\AccessControl',
    'rules' => [
        [
            'controllers'=>['site'],           
            'actions' => ['logout'],
            'allow' => true,
            'roles' => ['@'],                                                  
        ],
    ]
]
```

И так, когда все настройки применены, миграция создана, можно запустить на выполнение:

```
php yii migrate

Yii Migration Tool (based on Yii v2.0.3)

Total 1 new migration to be applied:
        m150513_054155_create_asto_tables

Apply the above migration? (yes|no) [no]:yes
*** applying m150513_054155_create_asto_tables
    > create table {{%star}} ... done (time: 0.059s)
    > create table {{%planet}} ... done (time: 0.041s)
    > create table {{%satellite}} ... done (time: 0.046s)
*** applied m150513_054155_create_asto_tables (time: 0.204s)


Migrated up successfully.
```

**Таблицы готовы**.
![screen1.1-1.jpg](assets/screen1.1-1.jpg)

 
Создадим модели, <a href="/yii2-app-advanced/backend/web/index.php?r=gii/default/view&id=model" target="_blank">
через Gii.</a>

![screen1.1-2.jpg](assets/screen1.1-2.jpg)

Используя это изображение, остальные модели `Planet` и `Satellite` создайте самостоятельно. После в директории
`yii2-app-advanced/common/models` появятся три класса `Planet.php`, `Star.php`, `Satellite.php`.

#### Описание реляционных данных.

Открыв `Star.php`, вы обнаружите новый метод, с которым до сих пор мы не встречались:

```php
 /**
 * @return \yii\db\ActiveQuery
 */
public function getPlanets()
{
    return $this->hasMany(Planet::className(), ['star_id' => 'id']);
}
```

Из названия `getPlanets` можно понять, что данный метод должен возвращать модел**и** планет. Ну и в реализации,
используется `$this->hasMany(Planet...`, который обозначает, что модель имеет много планет. Можно догадаться, что в моделях
`Planet.php` и `Satellite.php` также должны быть похожие методы, которые описывают связи между моделями. И правда:

```php
// в Planet.php

public function getStar()
{
    return $this->hasOne(Star::className(), ['id' => 'star_id']);
}

public function getSatellites()
{
    return $this->hasMany(Satellite::className(), ['planet_id' => 'id']);
}
```

```php
// в Satellite.php

public function getPlanet()
{
    return $this->hasOne(Planet::className(), ['id' => 'planet_id']);
}
```

#### Доступ к реляционным данным.

Из описания методов, которые описаны выше, можно увидеть, что возвращается объект 
<a href="http://www.yiiframework.com/doc-2.0/yii-db-activequery.html" target="_blank">\yii\db\ActiveQuery</a>.

```php
/**
* @return \yii\db\ActiveQuery
*/
public function getPlanet() {..}
```

Для того, что получить все спутники для планеты Марс, нужно обратиться к коду:

```php
$marsModel = Planet::find()->where(['name'=>'Марс'])->one();
$marsModel->getSatellites()->all();
```

Например, у Юпитера 67 спутников, а нужно получить только 10 первых, которые отсортированы по имени:

```php
$marsModel = Planet::find()->where(['name'=>'Юпитер'])->one();
$marsModel->getSatellites()->limit(10)->orderBy(['name'=>SORT_ASC])->limit(10)->all();
```

Результатом будет массив Active Record моделей. Иногда, для экономии памяти, результат стоит возвращать в виде массива
значений с помощью `->asArray()->all()`.

Все эти примеры выполняли в два этапа: находилась модель, находились отношения, если в этом была необходимость.
Можно сделать тоже самое в один запрос:

```php
$marsModel = Planet::find()->with('satellites')->where(['name'=>'Юпитер'])->one();
```

Уже упоминалось, что почти каждый класс в Yii наследует `yii\base\Object`. Это означает, что к любому методу, который
начинающийся как get(геттер) или set(сеттер), может быть использован как свойство объекта. Т.е. `getPlanet()` в модели 
`Satellite` может быть получено как `$satelliteModel->planet`:

```php
$marsModel = Planet::find()->where(['name'=>'Марс'])->one();
$marsModel->getSatellites()->all();
```

эквивалентно

```php
$marsModel = Planet::find()->where(['name'=>'Марс'])->one();
$marsModel->satellites; //вернёт массив Active Record моделей Satellites
```

#### Вывод реляционных данных в видах.
 
Сгенерируйте <a href="/yii2-app-advanced/backend/web/index.php?r=gii/default/view&id=crud" target="_blank">
через CRUD Gii.</a> виды и контроллер для моделей `Star`, `Planet` и `Satellite`. В качестве подсказки воспользуйтесь
следующим изображением:

![screen1.1-3.jpg](assets/screen1.1-3.jpg)

Обратите внимание, что модели `Star`, `Planet` и `Satellite` мы располагаем в пространстве имён `common\models`, которое
подразумевает доступность моделей из frontend и backend. А вспомогательные модели для фильтрации и сортировки
`Search Model Class` в пространстве в `backend\models`, т.к. определённая фильтрация и сортировка понадобится только 
в backend приложении.

Чтобы убедиться в правильности выполненных действий выполните тесты.

Из `yii2-tutorial\yii2-app-advanced\tests\codeception\bin` установите миграцию для тестовой базы

```
php yii migrate
```

Из `yii2-tutorial\yii2-app-advanced\tests\codeception\backend` выполните две команды для запуска тестов:

- создайте исполнителей для тестов

```
codecept build
```

- запустите функциональный тест AstroCept

```
codecept run functional functional\AstroCept.php

Time: 519 ms, Memory: 21.00Mb
OK (1 test, 3 assertions)
```

После того, как Gii сгенерировал контроллеры и виды, станут доступны следующие url:

- <a href="/yii2-app-advanced/backend/web/index.php?r=star" target="_blank">управление Star</a>.
- <a href="/yii2-app-advanced/backend/web/index.php?r=planet" target="_blank">управление Planet</a>.
- <a href="/yii2-app-advanced/backend/web/index.php?r=satellite" target="_blank">управление Satellite</a>.

Эти страницы служат интерфейсом, отправной точкой для работы с моделями. С этих страниц можно попасть на формы для
создания или изменения информации по звёздам, планетам и их спутникам. На данный момент база данных не содержит 
какой-либо информации по звёздам, планетам и их спутникам. Перейдите на следующий шаг, в котором в базу данных 
добавлена эта информация:

```
git checkout -f step-1.2
```

Обновив страницу управления планетами можно увидеть информацию:

<img src="/scripts/assets/screen1.2-1.jpg" class="img-responsive">

Давайте приведём её к более красивому виду. Для настройки вида откройте файл `yii2-app-advanced/backend/views/planet/index.php`
Вначале измените `title` компонента `View`:

```php
$this->title = 'Планеты';
```

Измените название ссылки

```php
<?= Html::a('Добавить планету', ['create'], ['class' => 'btn btn-success']) ?>
```

Ну и наконец измените структуру колонок в таблице на:

```php
'columns' => [
    'id',
    [
        'attribute'=>'name',
        'label'=>'Планета',
    ],
    [
        'label'=>'Звезда',
        'attribute'=>'star_id',
        'value' => function($planet) {
            return $planet->star->name;
        }
    ],
    [
        'label'=>'Количество спутников',      
        'value' => function($planet) {
            return $planet->getSatellites()->count();
        }
    ],
    ['class' => 'yii\grid\ActionColumn'],
],
```

тут мы определили пять колонок:
- первая `id` для отображения служебной информации по id записям планет.
- вторая `name` в виде массива `attribute`, который указывает, что значение для колонки требуется брать из атрибута `name`,
но заголовок у этой колонки по-умолчанию `Name`(Наименование). Изменим на `'header'=>'Планета',`
- третья колонка содержала числовое значение `star_id`, что не совсем удобно. Так как у модели Planet настроена связь
```php
public function getStar()
{
    return $this->hasOne(Star::className(), ['id' => 'star_id']);
}
```
, то значение колонки `value` можно изменить на анонимную функцию, которая будет возвращать наименование звезды.
- четвертая колонка будет отображать количество спутников у планеты. Только в анонимной функции мы обратились непосредственно
к методу `Planet::getSatellites()`, так как `$planet->star` через магический метод `__get` получает массив моделей, 
который в данном пункте избыточен, в отличие от предыдущего.
- ну и пятая колонка `ActionColumn`, выводит три ссылки, для стандартных операций с моделью Planet (просмотр, 
редактирование, удаление).
- была также колонка `yii\grid\SerialColumn`, которая служит для вывода порядкового номера строки. Мы её удалили за
ненадобностью.

Аналогично попробуйте настроить виды для оставшихся моделей `Star`(`/views/star/index.php`) и `Satellite`(`/views/satellite/index.php`).

<img src="/scripts/assets/screen1.2-2.jpg" class="img-responsive">

Вы наверное уже обратили внимание, что только у колонки "Количество спутников" отсутствует поле для фильтрации, а также
недоступна сортировка. Всё потому, что мы не указали свойство `attribute` для этой колонки. Чтобы это исправить добавим 
его:

```php
[
    'label'=>'Количество спутников',
    'attribute'=>'countSatellites',
    'value' => function($planet) {
        return $planet->getSatellites()->count();
    }
],
```

Но обновив страницу ничего не изменится - мало указать `attribute`, нужно указать, что это свойство является безопасным, 
в `rules()` модели для поиска, т.е. в `/backend/models/SearchPlanet.php`.

```php
class SearchPlanet extends Planet
{
    public $countSatellites;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'star_id', 'countSatellites'], 'integer'],
            [['name'], 'safe'],
        ];
    }
    
```

Теперь на странице появилось поле input для фильтрации, но фильтрация и сортировка по этому полю по-прежнему не работает.
Осталось настроить `$dataProvider`, а именно свойство `$sort` и `$query` 
<a href="http://www.yiiframework.com/doc-2.0/yii-data-activedataprovider.html" target="_blank">yii\data\ActiveDataProvider</a>.
Сортировка и фильтрация выполняется путём добавления к sql запросу `ORDER BY` или `WHERE`. Т.е. когда сработает сортировка по полю
`countSatellites`, то должен выполнится запрос

```sql
SELECT * FROM Planet ORDER BY countSatellites;
```

Так как в таблице Planet нету поля `countSatellites`, то такой запрос не выполнится. Поэтому нужно изменить запрос,
чтобы в нём участвовал `countSatellites`. На данный момент в методе `search()` модели `backend\models\SearchPlanet` у нас:
 
```php
$query = Planet::find(); // эквивалентно выполнению SELECT * FROM Planet
```

Нам нужно изменить его так, чтобы в запросе участвовало `countSatellites`:

```sql
SELECT planet.*, count(planet_id) as countSatellites 
FROM planet 
LEFT JOIN satellite ON planet_id = planet.id 
GROUP BY planet.id 
ORDER BY countSatellites DESC; 
```

Когда есть sql запрос, то не составит труда его переделать в `$query` (ActiveQuery):

```php
$query = Planet::find()
        ->select([$this->tableName() . '.*', 'count(planet_id) as countSatellites'])
        ->joinWith('satellites')
        ->groupBy($this->tableName() . '.id');
```

Теперь сортировка работает для всех полей. Проверьте - <a href="/yii2-app-advanced/backend/web/index.php?r=planet" target="_blank">управление Planet</a>.
Осталось настроить фильтрацию для поля `countSatellites`. Так в sql запросе `countSatellites` - это агрегатные функция 
`COUNT()`, то для неё параметр запроса `WHERE` не сработает, необходим `HAVING`. Для ActiveQuery это эквивалентно вызову
метода `$query->having()`. Но параметр `HAVING` понадобится только, когда поле фильтра будет заполнено.
С учётом всего этого  метод `search()` модели `backend\models\SearchPlanet` примет следующий вид:

```php
public function search($params)
{
    $query = Planet::find()
        ->select([$this->tableName() . '.*', 'count(planet_id) as countSatellites'])
        ->joinWith('satellites')
        ->groupBy($this->tableName() . '.id');

    $dataProvider = new ActiveDataProvider(
        [
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'id',
                    'name',
                    'star_id',
                    'countSatellites' => [
                        'asc' => ['countSatellites' => SORT_ASC,],
                        'desc' => ['countSatellites' => SORT_DESC,],
                    ],
                ]
            ]
        ]
    );

    $this->load($params);

    if (!$this->validate()) {
        return $dataProvider;
    }

    if ($this->countSatellites) {
        $query->having(['countSatellites' => (int) $this->countSatellites]);
    }

    $query->andFilterWhere(
        [
            $this->tableName() . '.id' => $this->id,
            'star_id' => $this->star_id,
        ]
    );

    $query->andFilterWhere(['like', 'name', $this->name]);

    return $dataProvider;
}
```

Для закрепления знаний, настройте фильтрацию и сортировку для дополнительного свойства `countPlanets` в модели 
`StarSearch` на странице <a href="/yii2-app-advanced/backend/web/index.php?r=star" target="_blank">управления моделями Star</a>.

#### Дополнительная информация для самостоятельного ознакомления:

- <a href="#" target="_blank">Официальное руководство по работе с связями в AR</a>.