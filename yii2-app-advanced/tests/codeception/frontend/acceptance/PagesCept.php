<?php
use tests\codeception\frontend\AcceptanceTester;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that static pages works');
$I->amOnPage(Yii::$app->urlManager->createAbsoluteUrl(['site/page', 'view' => 'about']));
$I->seeInTitle('О нас!');
$I->see('Это статическая страница, которая может быть изменена в файле:');

$I->amOnPage(Yii::$app->urlManager->createAbsoluteUrl(['site/page', 'view' => 'duty']));
$I->seeInTitle('Режим работы!');
$I->see('Это статическая страница, которая может быть изменена в файле:');

$I->amOnPage(Yii::$app->urlManager->createAbsoluteUrl(['site/page', 'view' => 'delivery']));
$I->seeInTitle('Доставка!');
$I->see('Это статическая страница, которая может быть изменена в файле:');

