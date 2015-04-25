### Работа с формами

В этом разделе рассмотрим как создать форму, как обрабатывать данные из формы.

<p class="alert alert-info">О работе с формами в
<a href="https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/start-forms.md" target="_blank">официальном
руководстве</a>
</p>


Чтобы начать, выполните команду из директории yii2-tutorial

```
git checkout -f step-0.2
```

Как выглядит форма, созданная с помощью Yii2,  можно увидеть <a href="/yii2-app-advanced/frontend/web/index.php?r=site%2Fcontact" target="_blank">по ссылке</a>.
По адресу ссылки `index.php?r=site%2Fcontact` смотрим, что используется всё тот же `SiteController` контроллер,
 что и в предыдущем уроке. Но тут уже действие другое - `contact`. Открываем `\frontend\controllers\SiteController::actionContact`:

```php
public function actionContact()
{
    $model = new ContactForm();
    if ($model->load(Yii::$app->request->post()) && $model->validate()) {
        if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('success', 'Спасибо за ваше письмо. Мы свяжемся с вами в ближайшее время.');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка отправки почты.');
        }

        return $this->refresh();
    } else {
        return $this->render('contact', [
            'model' => $model,
        ]);
    }
}
```

Видим какой-то не понятный код и знакомый уже `$this->render`. Видно, что в данном случае используется вид `'contact'`.
Открываем вид - `yii2-app-advanced/frontend/views/site/contact.php`. Ещё больше не понятного кода. Давайте разбираться.

И так, чтобы создать html код формы:

```html
<form action="..." method="post">
    <input ...>
    <input ...>
    <input ...>
    <button>
</form>
```

нужно обратиться за помощью к виджету `\yii\widgets\ActiveForm`.