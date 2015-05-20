<?php
use tests\codeception\backend\FunctionalTester;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
$I->wantTo('ensure than create form works');
$formPage = \tests\codeception\backend\_pages\PlanetFormPage::openBy($I);

$I->fillField('//*[@id="planet-name"]', 'Земля');
$I->selectOption('//*[@id="planet-star_id"]', 'Солнце');
$I->click('//*[@id="w0"]/div[3]/button');
$I->dontSeeInTitle('Новая планета');