<?php
use tests\codeception\frontend\AcceptanceTester;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that home page works');
$I->amOnPage(Yii::$app->homeUrl);
$I->see('Мой сайт');
$I->seeLink('О нас');
$I->click('О нас');
$I->see('Это статическая страница, которая может быть изменена в файле:');
