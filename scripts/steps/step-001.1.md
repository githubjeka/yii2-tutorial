### Работа с реляционными данными

Вы уже познакомились с тем как с помощью Active Record можно получить запись из базы данных. В этом разделе
научимся получать связанные данные и работать с ними.

Чтобы начать, выполните команду из директории yii2-tutorial:

```
git checkout -f step-1.1
```

Сперва определимся, что мы хотим получить. Возьмём нашу солнечную систему. В солнечной системе есть звезда Солнце, 
вокруг звезды вращаются планеты - Меркурий, Венера, Земля, Марс, Церера, Юпитер, Сатурн, Уран, 
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
ключи - "id". Также лучше называть сущность в lowercase или under_score, т.к. например при работе с PostgreSQL 
UpperCase таблицы и поля нужно обрамлять двойными кавычками. <a href="https://toster.ru/q/139295" target="_blank">Подробнее...</a>
</p>

Создадим эти таблицы, через миграцию. Выполните в `yii2-tutorial\yii2-app-advanced`:

```
php yii migrate/create create_asto_tables

    Yii Migration Tool (based on Yii v2.0.3)
    Create new migration '/yii2-tutorial/yii2-app-advanced/console/migrations/m150513_054155_create_asto_tables.php'? (yes|no) [no]:yes
    New migration created successfully.
```

Новая миграция создана в `\yii2-tutorial\yii2-app-advanced\console\migrations\m150620_054003_create_asto_tables.php`
Приведём её код к следующему виду:

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
                . $this->db->quoteTableName('{{%star}}') . '(id) ON UPDATE CASCADE ON DELETE CASCADE'
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
                . $this->db->quoteTableName('{{%planet}}') . '(id) ON UPDATE CASCADE ON DELETE CASCADE'
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
именах таблиц, который можно установить через конфигурацию компонента `db` в `\yii2-tutorial\yii2-app-advanced\common\config\main-local.php`:

```php
 'db' => [
    'class' => 'yii\db\Connection',
    'dsn' => 'sqlite:' . __DIR__ . '/../../sqlite.db',
    'tablePrefix' => 'astro',
],
```

Так как наша миграция, может в будущем использоваться не только на SQLite, но и на Mysql, то для Mysql с помощью
`$tableOptions` устанавливаем кодировку и `ENGINE=InnoDB`, для работы с внешними ключами `FOREIGN KEY`. В SQLite по умолчанию 
<a href="https://www.sqlite.org/foreignkeys.html#fk_enable" target="_blank">проверка внешних ключей отключена</a>.
Для того, чтобы её включить необходимо выполнить команду:

```
PRAGMA foreign_keys = ON;
```

Выполнять её требуется всякий раз, когда устанавливается соединение с базой данных. У класса, который в нашем случае
отвечает за соединение, <a href="http://www.yiiframework.com/doc-2.0/yii-db-connection.html" target="_blank">yii\db\Connection</a>
есть события:

- `EVENT_AFTER_OPEN` - срабатывает каждый раз, после установки соединения с БД.
- `EVENT_BEGIN_TRANSACTION` - срабатывает каждый раз, перед началом транзакции.
- `EVENT_COMMIT_TRANSACTION` - срабатывает каждый раз, после применении транзакции. 
- `EVENT_ROLLBACK_TRANSACTION` - срабатывает каждый раз, после отмены транзакции.

Присоединим на событие `EVENT_AFTER_OPEN` функцию-обработчик, которая будет включать проверку внешних ключей в SQLite.
Это можно сделать, через глобальную конфигурацию компонента. Добавьте к настройкам базы данных `on afterOpen`:

```php
'db' => [
    'class' => 'yii\db\Connection',
    'dsn' => 'sqlite:' . __DIR__ . '/../../sqlite.db',   
    'on afterOpen' => function ($event) {
        $event->sender->createCommand('PRAGMA foreign_keys = ON;')->execute();
    }
],
```

<p class="alert alert-info">Освежите знания <a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/concept-events.md" target="_blank">
о событиях в Yii 2</a>
</p>

Заметьте, что таким способом (`'on имя_события'=>обработчик`) можно присоединять обработчики к любым событиям 
компонентов или приложений. Так же можно можно поступить и с поведениями. Например, запретить доступ "гостям" к 
методу `logout` в контроллере `site`, можно с помощью `as access`:

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

<img src="/scripts/assets/screen1.1-1.jpg" class="img-responsive">
 
Создадим модели, <a href="/yii2-app-advanced/backend/web/index.php?r=gii/default/view&id=model" target="_blank">
через Gii.</a>

<img src="/scripts/assets/screen1.1-2.jpg" class="img-responsive">

Используя это изображение, остальные модели -`Planet` и `Satellite`, создайте самостоятельно. После в директории
`yii2-app-advanced/common/models` появятся три файла `Planet.php`, `Star.php`, `Satellite.php`, которые описывают модели.

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
используется `$this->hasMany(Planet...`, который обозначает, что звезда имеет много планет. Можно догадаться, что в моделях
`Planet.php` и `Satellite.php` также должны быть похожие методы, которые описывают связи между моделями. Точно:

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

Метод `hasOne` из <a href="http://www.yiiframework.com/doc-2.0/yii-db-activerecord.html" target="_blank">ActiveRecord</a>
в отличие `hasMany` обозначает отношение моделей, как связь один к одному. Например спутник Луна принадлежит только одной
планете Земля.

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

Эти примеры выполняли в два этапа: находилась модель, находились отношения, если в этом была необходимость.
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

#### Дополнительная информация для самостоятельного ознакомления:

- <a href="#" target="_blank">Официальное руководство по работе с связями в AR</a>.