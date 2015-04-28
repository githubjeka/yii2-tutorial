<?php
use tests\codeception\frontend\FunctionalTester;

/* @var $scenario Codeception\Scenario */

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that home page works');
$I->amOnPage(Yii::$app->homeUrl);
$I->see('Мой сайт');
$I->seeLink('О нас');
$I->click('О нас');
$I->see('Это статическая страница, которая может быть изменена в файле:');
