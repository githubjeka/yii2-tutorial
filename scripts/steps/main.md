###Создание сайта с использованием <a href="http://www.yiiframework.com/" target="_blank">Yii</a> 2.x

В данном учебнике описывается процесс создания сайта. Каждый шаг разработки описан максимально подробно и может 
быть применён при создании других приложений. В дополнение к 
<a href="http://www.yiiframework.com/doc-2.0/guide-index.html" target="_blank">полному руководству</a>
и <a href="http://www.yiiframework.com/doc-2.0/index.html" target="_blank">API</a>, данное пособие показывает,
вместо полного и подробного описания, пример практического применения фреймворка Yii.

Для того, чтобы выполнять упражнения из учебника понадобятся инструменты 
<a href="https://getcomposer.org/" target="_blank">composer</a> и 
<a href="http://git-scm.com/" target="_blank">git</a>. Не отчаивайтесь, если вам не известны эти инструменты, 
нужно будет лишь выполнить несколько команд, которые будут описаны.

Разработчики данного интерактивного курса:
- Евгений Ткаченко (<et.coder@gmail.com>)

Сообщество Yii

- [Форум](http://www.yiiframework.com/forum/)
- [GitHub](https://github.com/yiisoft/yii2)
- [Facebook](https://www.facebook.com/groups/yiitalk/)
- [Twitter](https://twitter.com/yiiframework)
- [LinkedIn](https://www.linkedin.com/groups/yii-framework-1483367)
        
### Начальная установка

Установим стартовый шаблон приложения 
<a href="https://github.com/yiisoft/yii2-app-advanced" target="_blank">[Yii 2 Advanced Project Template]</a>.
Для этого необходимо выполнить команды, из корневой директории учебника(yii2-tutorial):

```
composer global require "fxp/composer-asset-plugin:1.0.0"
composer create-project --prefer-dist yiisoft/yii2-app-advanced yii2-app-advanced
```

<p class="alert alert-info">
Если возникают сложности, то ознакомьтесь с 
<a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/start-installation.md" target="_blank">
официальным руководством</a>
</p>

Процесс установки шаблона первый раз длительный, поэтому давайте пока познакомимся со структурой учебника.
Для этого откройте директорию `yii2-tutorial` и осмотритесь:

```
/scripts/                -> Скрипты для работы учебника
/yii2-app-advanced/      -> Сюда будет загружено демонстрационное приложение
/.gitignore              -> Файл конфигурация, для Git
/readme.md               -> Начальное описание учебника
```

После того как процесс установки демонстрационного приложения будет окончен, необходимо запустить, инициализировать
приложение, выполнив команду из директории `/yii2-app-advanced`. 

```
php init --env=Development
```

После этого будут сгенерированы некоторые файлы для работы всего приложения и будет установлен режим отладки.
Теперь вы можете перейти по <a href="/yii2-app-advanced/frontend/web/" target="_blank">ссылке</a>, чтобы убедится в 
работоспособности вашего сайта.

#### Дополнительная информация для самостоятельного ознакомления:

- Ознакомьтесь с информацией об Yii
<a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/intro-yii.md" target="_blank">официальном
руководстве</a>.

- Ознакомьтесь с информацией о запуске приложения
<a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/start-workflow.md" target="_blank">официальном
руководстве</a>.