### Статические страницы

В этом разделе рассмотрим как создать новую страницу со статическим текстом.

Чтобы начать, выполните команду из директории yii2-tutorial

```
git checkout -f step-0.1
```
и ознакомьтесь с 
<a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/structure-views.md#%D0%A0%D0%B5%D0%BD%D0%B4%D0%B5%D1%80%D0%B8%D0%BD%D0%B3-%D1%81%D1%82%D0%B0%D1%82%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D1%85-%D1%81%D1%82%D1%80%D0%B0%D0%BD%D0%B8%D1%86-" target="_blank">
официальной документацией по статическим страницам.
</a>

Теперь перейдя <a href="http://localhost:9000/yii2-app-advanced/frontend/web/index.php?r=site%2Fabout" target="_blank">
по ссылке</a> вы попадёте на статическую страницу "About".

Если посмотреть на адрес ссылки, то можно увидеть `index.php?r=site%2Fabout`.
`index.php` это входной скрипт нашего приложения. Именно через него идут все запросы пользователя на исполнение.
Дальше связка `site%2Fabout (эквивалентно site/about)`. `site` - имя контроллера, который обрабатывает наш запрос, 
`about` - действие, в контроллере которое мы вызываем. Т.е., внутри, Yii переделывает `site` в класс `SiteController`, 
а `about` в метод `function actionAbout() {...}` и вызывает его на исполнение.

Найдём этот контроллер и этот метод. Контроллер лежит в `yii2-app-advanced/frontend/controllers/SiteController.php`, 
по-умолчанию все контроллеры принято располагать в папке `controllers/` c суффиксом `Controller` 
(<a href="https://github.com/yiisoft/yii2/issues/2709" target="_blank">Почему так?</a>).

