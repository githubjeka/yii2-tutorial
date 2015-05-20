### Вывод реляционных данных в видах.
 
Сгенерируйте <a href="/yii2-app-advanced/backend/web/index.php?r=gii/default/view&id=crud" target="_blank">
через CRUD Gii.</a> виды и контроллер для моделей `Star`, `Planet` и `Satellite`. В качестве подсказки воспользуйтесь
следующим изображением:

<img src="/scripts/assets/screen1.1-3.jpg" class="img-responsive">

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

Обновив страницу управления планетами, можно увидеть информацию:

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
- первая `id`, служит для отображения служебной информации по id записям планет.
- вторая `name` в виде массива `attribute`, который указывает, что значение для колонки требуется брать из атрибута `name`,
но заголовок у этой колонки по умолчанию `Name`(Наименование). Изменим на `'label'=>'Планета',`
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
- ну и пятая колонка <a href="http://www.yiiframework.com/doc-2.0/yii-grid-actioncolumn.html" target="_blank">ActionColumn</a>,
выводит три ссылки, для стандартных операций с моделью Planet (просмотр, редактирование, удаление).
- была также колонка <a href="http://www.yiiframework.com/doc-2.0/yii-grid-serialcolumn.html" target="_blank">yii\grid\SerialColumn</a>,
которая служит для вывода порядкового номера строки. Мы её удалили за ненадобностью.

Аналогично попробуйте настроить виды для оставшихся моделей `Star`(`/views/star/index.php`) и `Satellite`(`/views/satellite/index.php`).

<img src="/scripts/assets/screen1.2-2.jpg" class="img-responsive">

Вы наверное уже обратили внимание, что только у колонки "Количество спутников" отсутствует поле для фильтрации, а также
недоступна сортировка. Всё потому, что мы не указали свойство `attribute` для этой колонки. Исправим это:

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
SELECT * FROM Planet ORDER BY countSatellites ASC|DESC;
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
ORDER BY countSatellites ASC|DESC;
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