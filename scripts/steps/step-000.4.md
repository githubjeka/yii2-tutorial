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

Теперь давайте вернёмся к форме "Опрос". Для работы с формой в клиентской части(далее frontend) мы использовали 
Active Record модель `Interview`, которая описывала форму. Т.к. эта модель описана в frontend, то в backend
она не доступна. Чтобы исправить это, необходимо модель расположить в общей директории - `common/models/`. 
Необходимо скопировать файл `Interview.php` из `frontend/models` в `common/models/`. Это уже сделано.

Вам осталось изменить файлы следующим образом. В common модели изменим пространство имени, удалим свойство "проверочный
код", удалим правила, так как это всё требуется на стороне frontend части. А в frontend модели изменить 
родительский класс с `\yii\db\ActiveRecord` на `\common\models\Interview` и удалите методы `tableName()` и `attributeLabels()`.

Теперь, когда все изменения внесены, в backend возможно использовать модель `\common\models\Interview`. Создадим вид,
в котором будут отображаться все записи из таблицы "Опросов". Чтобы облегчить выполнение этой задачи, 
<a href="http://localhost:9000/yii2-app-advanced/backend/web/index.php?r=gii" target="_blank">обратимся к Gii</a>.
Выберите генератор "CRUD Generator", который генерирует виды и контроллер на основании модели. Введите в Model Class 
`common\models\Interview`, а в Controller Class - `backend\controllers\InterviewController`. Всё, жмите Preview.
"CRUD Generator" генерируется вид для создания, изменения, удаления и просмотра модели, также помогает генерировать 
страницу `index.php`, которая показывает список моделей постранично, используя виджет 
<a href="http://www.yiiframework.com/doc-2.0/yii-grid-gridview.html" target="_blank">GridView</a> или 
<a href="http://www.yiiframework.com/doc-2.0/yii-widgets-listview.html" target="_blank">ListView</a>
Нажимаем "Generate" и наслаждаемся <a href="/yii2-app-advanced/backend/web/index.php?r=interview" target="_blank">
результатами работы</a>



TODO: описать виджет GridView, ограничить доступ
 