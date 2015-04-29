<?php
use tests\codeception\frontend\_pages\InterviewPage;
use tests\codeception\frontend\FunctionalTester;

/* @var $scenario Codeception\Scenario */

$I = new FunctionalTester($scenario);
$I->wantTo('быть уверенным, что страница с формой "опрос" работает.'); //wantTo - хочу
$interviewPage = InterviewPage::openBy($I);
$I->amGoingTo('отправить форму без данных'); //amGoingTo - собираюсь

$interviewPage->submit([]);

$I->expectTo('увидеть ошибки валидации'); //expectTo - ожидаю
$I->see('Необходимо заполнить «Имя».', '.help-block');
$I->see('Необходимо заполнить «Пол».', '.help-block');
$I->see('Необходимо заполнить «Какие планеты обитаемы?».', '.help-block');
$I->see('Необходимо заполнить «Какие космонавты известны?».', '.help-block');
$I->see('Необходимо заполнить «Проверочный код».', '.help-block');

$I->amGoingTo('отправить форму c некорректным проверочным кодом'); //amGoingTo - собираюсь
$interviewPage = InterviewPage::openBy($I);
$interviewPage->submit([
    'verifyCode' => 'wrongText',
]);

$I->expectTo('увидеть ошибки валидации каптчи'); //expectTo - ожидаю
$I->see('Неправильный проверочный код.', '.help-block');

$I->amGoingTo('отправить форму c корректными данными'); //amGoingTo - собираюсь
$interviewPage->submit([
    'name' => 'Иванов',
    'sex' => '1',
    'planets' => [1,2,3],
    'astronauts' => [1,2,3],
    'planet' => 1,
    'verifyCode' => 'testme',
]);

$I->expectTo('не увидеть ошибки валидации'); //expectTo - ожидаю
$I->see('Спасибо, что уделили время. В ближайшее время будут опубликованы результаты.');
$I->dontSee('Необходимо заполнить «Имя».', '.help-block');
$I->dontSee('Необходимо заполнить «Пол».', '.help-block');
$I->dontSee('Необходимо заполнить «Какие планеты обитаемы?».', '.help-block');
$I->dontSee('Необходимо заполнить «Какие космонавты известны?».', '.help-block');
$I->dontSee('Необходимо заполнить «Проверочный код».', '.help-block');
$I->dontSee('Пол выбран не верно.', '.help-block');
$I->dontSee('Выбран не корректный список планет.', '.help-block');
$I->dontSee('Выбран не корректный список космонавтов.', '.help-block');
$I->dontSee('Неправильный проверочный код.', '.help-block');

$I->amGoingTo('открыть форму второй раз'); //amGoingTo - собираюсь
$interviewPage = InterviewPage::openBy($I);
$I->see('Доступ ограничен. Вы ранее совершали действия на этой странице.');