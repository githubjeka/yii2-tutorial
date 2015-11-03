### Административное приложение Backend

В этом разделе добавим функциональности в backend приложение. Создадим место, где администратор сможет просматривать
результаты опроса. Рассмотрим возможности ограничения доступа к тому или иному функционалу.

Чтобы начать, выполните команду из директории yii2-tutorial:

```
git checkout -f step-0.4
```

Входной файл административного раздела (далее Backend) доступен по ссылке 
<a href="/yii2-app-advanced/backend/web/index.php?r=site/logout" target="_blank">
/yii2-app-advanced/backend/web/index.php</a>, а все файлы для работы backend располагаются в директории 
`/yii2-app-advanced/backend/`.
 
Если вы не прошли аутентификацию на сайте, то в backend вас не пустит. В работу вступил так называемый фильтр 
контроллера <a href="http://www.yiiframework.com/doc-2.0/yii-filters-accesscontrol.html" target="_blank">
yii\filters\AccessControl</a>. Фильтры являются особым видом поведений, которые могут быть выполнены до действия
контроллера или после.

Если открыть `SiteController` backend части, то можно обнаружить следующий код:
 
```php
return [
    'access' => [
        'class' => AccessControl::className(),
        'rules' => [
            [
                'actions' => ['login', 'error'],
                'allow' => true,
            ],
            [
                'actions' => ['logout', 'index'],
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],
];
```

С помощью правил доступа `rules` можно описать к каким действиям контроллера применять те или иные ограничения. 

<p class="alert alert-info">Подробная информация по работе фильтров описана в
<a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/structure-filters.md" target="_blank">официальном
руководстве</a>
</p>

TODO: https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/security-authentication.md

Теперь давайте вернёмся к форме "Опрос". Для работы с формой в клиентской части(далее frontend) мы использовали 
Active Record модель `Interview`, которая описывала форму. Т.к. эта модель описана в frontend, то в backend
к ней можно обратиться как `frontend/models/Interview` - что не совсем корректно.
Чтобы исправить это, необходимо модель расположить в общей директории - `common/models/`. 
Необходимо переместить файл `Interview.php` из `frontend/models` в `common/models/`. Это уже сделано.

Вам осталось изменить файлы, зависимые от этой модели, следующим образом:

- в common модели `yii2-app-advanced/common/models/Interview.php` изменить пространство имени класса.

- поменять в `frontend/controllers/SiteControllers.php` в методе `actionInterview()` пространство имени для этой модели
 с `frontend/models/Interview` на `common/models/Interview`.

- поменять в виде `yii2-app-advanced/frontend/views/site/interview.php` пространство имени, также как в предыдущем случае.

- удалить файл `yii2-app-advanced/frontend/models/Interview.php`

Теперь, когда все изменения внесены, в backend и frontend возможно использовать единую AR модель - `\common\models\Interview`.

<p class="alert alert-info">
Для того, чтобы избежать такой ситуации в будущем, лучше сразу генерировать все модели Active Record в "common/models".
</p>

#### Важный момент: Разделение моделей Active Record

Очень часто, модель Active Record разбивается на два, три класса, т.е. `\common\models\Interview` и её наследники
`\frontend\models\Interview` и `\backend\models\Interview`. Сперва это кажется логичным и правильным. Но такое разделение
влечёт за собой скрытые проблемы. Возможно в большинстве случаев вы даже не столкнётесь с этими проблемами, но они существуют.
Вот некоторые из них:

- может возвратиться некорректная связь с другими разделёнными моделями, вместо ожидаемой `backend/models/Interview`
 вам будет доступна только `common/models/Interview` и будет сложно исправить сложившеюся ситуацию.

- изменения общей модели, может оказать негативное влияние на работу модели из другого модельного слоя (frontend, backend). 
Такое на практике встречается очень часто, после правок в общей - ломается что-то критичное на backend.

- проблемы с перекрытием событий: beforeValidate. Выливается в проблемы с перекрытием сценариев при валидации.
Придётся создавать "костыльный" код `return Model::scenarios();`

Единственным плюсом от разделения это не нужно дублировать атрибуты модели, для остальных моделей, которые обрабатывают AR.
Но этот плюс теряется за кучей недостатков. В общем постарайтесь придерживаться логики , что модельный слой (Active Record)
должен быть единым для разных частей приложения (backend, frontend, rest и т.д.). Поэтому работая с yii2-advanced располагайте
все Active Record модели в `common/models/`. Или создавайте отдельные для frontend и backend, если уж логика настолька
различна и её нужно разделить. 

#### Виджет GridView

Создадим вид, в котором будут отображаться все записи из таблицы "Опросов". Чтобы облегчить выполнение этой задачи, 
<a href="/yii2-app-advanced/backend/web/index.php?r=gii" target="_blank">обратимся к Gii</a>.
Выберите генератор "CRUD Generator", который генерирует виды и контроллер на основании модели. Введите в Model Class 
`common\models\Interview`, а в Controller Class - `backend\controllers\InterviewController`. Всё, жмите Preview.
"CRUD Generator" генерируется вид для создания, изменения, удаления и просмотра модели, также помогает генерировать 
страницу `index.php`, которая показывает список моделей постранично, используя виджет 
<a href="http://www.yiiframework.com/doc-2.0/yii-grid-gridview.html" target="_blank">GridView</a> или 
<a href="http://www.yiiframework.com/doc-2.0/yii-widgets-listview.html" target="_blank">ListView</a>
Нажимаем "Generate" и наслаждаемся <a href="/yii2-app-advanced/backend/web/index.php?r=interview" target="_blank">
результатами работы</a>.

Очень часто необходимо вывести данные в виде таблицы. Для решения этой задачи в Yii имеется сверхмощный виджет
<a href="http://www.yiiframework.com/doc-2.0/yii-grid-gridview.html" target="_blank">yii\grid\GridView</a>. Разрабатывая
административный раздел сайта, практически всегда этот виджет будет полезен. Вот и сейчас в виде 
`yii2-app-advanced/backend/views/interview/index.php` он используется для того, чтобы отобразить все ответы на опрос.

```php
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        //...
    ],
]); ?>
```

Для работы этого виджета нужен объект `$dataProvider`, который реализует интерфейс
<a href="http://www.yiiframework.com/doc-2.0/yii-data-dataproviderinterface.html" target="_blank">yii\data\DataProviderInterface</a>
Интерфейс можно разделить на следующие части: набор данных; объект, который отвечает за сортировку данных; объект, который
отвечает за постраничную разбивку данных. Есть несколько реализаций этого интерфейса:

- <a href="http://www.yiiframework.com/doc-2.0/yii-data-activedataprovider.html" target="_blank">yii\data\ActiveDataProvider</a> 
- <a href="http://www.yiiframework.com/doc-2.0/yii-data-arraydataprovider.html" target="_blank">yii\data\ArrayDataProvider</a> 
- <a href="http://www.yiiframework.com/doc-2.0/yii-data-sqldataprovider.html" target="_blank">yii\data\SqlDataProvider</a> 

В нашем случае для моделей используется Active Record, поэтому и для удобства работы, лучше выбрать первый класс -
`yii\data\ActiveDataProvider`, так как это позволит представить набор данных в виде массива Active Record объектов.
$dataProvider создаётся в контроллере `yii2-app-advanced/backend/controllers/InterviewController.php`: 

```php
new ActiveDataProvider([
    'query' => Interview::find(), 
]);
```

<a href="http://www.yiiframework.com/doc-2.0/yii-db-activerecord.html#find()-detail" target="_blank">Interview::find()</a> - 
подготавливает запрос типа `SELECT * FROM interview`. Далее $dataProvider передаётся в вид, где срабатывают внутренние 
механизмы `ActiveDataProvider` для отображения данных, в соответствии с разбивкой на страницы и сортировкой - выполняется 
запрос типа

```
SELECT * FROM `interview` ORDER BY имя_атрибута LIMIT количество_записей_на_страницу OFFSET (номер_страницы - 1) * количество_записей_на_страницу
```

Разбивка записей по страницам и сортировка могут быть настроены следующим образом для датапровайдера:

```php
$dataProvider = new ActiveDataProvider([
    'query' => Interview::find(),
    'pagination' => [
        'pageSize' => 50,
    ],
    'sort' => [
        'defaultOrder' => [
            'name' => SORT_ASC,
        ]
    ]
]);
```

Когда данные получены из базы данных, то с учётом настроек свойства `columns` виджета GridView эти данные приобретают 
окончательный вид и выводятся на экран. Формат вывода данных может быть изменён путём изменения свойства 
<a href="http://www.yiiframework.com/doc-2.0/yii-grid-gridview.html#$columns-detail" target="_blank">columns</a>:

Вид `\yii2-tutorial\yii2-app-advanced\backend\views\interview\index.php`

```php
<?php
    $planets = ['Меркурий', 'Венера', 'Земля', 'Марс', 'Юпитер', 'Сатурн', 'Уран', 'Нептун'];

    $astronauts = [
        'Юрий Гагарин',
        'Алексей Леонов',
        'Нил Армстронг',
        'Валентина Терешкова',
        'Эдвин Олдрин',
        'Анатолий Соловьев'
    ];

    echo GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'name',
                [
                    'attribute' => 'sex',
                    'value' => function ($model) {
                        return $model->sex ? 'Мужчина' : 'Женщина';
                    }
                ],
                [
                    'attribute' => 'planets',
                    'value' => function ($model) use ($planets) {
                        $result = null;
                        $numbers = explode(',', $model->planets);
                        foreach ($numbers as $number) {
                            $result .= $planets[$number] . ' ';
                        }
                        return $result;
                    }
                ],
                [
                    'attribute' => 'astronauts',
                    'value' => function ($model) use ($astronauts) {
                        $result = null;
                        $numbers = explode(',', $model->astronauts);
                        foreach ($numbers as $number) {
                            $result .= $astronauts[$number] . ' ';
                        }
                        return $result;
                    }
                ],
                [
                    'attribute' => 'planet',
                    'value' => function ($model) use ($planets) {
                        return $planets[$model->planet];
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{delete}',
                ],
            ],
        ]
    ); ?>
```

Сейчас `InterviewController` не использует фильтры для ограничения доступов к своим действиям. Попробуйте самостоятельно
добавить условия, чтобы действия `actionIndex` и `actionDelete` могли выполнять только аутентифицированные пользователи.
Остальные действия (создание, изменение, просмотр опроса) `InterviewController` можно удалить за ненадобностью.

#### Дополнительная информация для самостоятельного ознакомления:

- <a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/structure-widgets.md" target="_blank">Виджеты</a>.
- <a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/output-pagination.md" target="_blank">Постраничное разделение данных</a>.
- <a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/output-sorting.md" target="_blank">Сортировка</a>.