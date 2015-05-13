<?php

use tests\codeception\backend\_pages\PlanetPage;
use tests\codeception\backend\_pages\SatellitePage;
use tests\codeception\backend\_pages\StarPage;
use tests\codeception\backend\FunctionalTester;

/* @var $scenario Codeception\Scenario */

$I = new FunctionalTester($scenario);

$I->wantTo('ensure Star page works');
$page = StarPage::openBy($I);
$I->seeInTitle('Stars');

$I->wantTo('ensure Planet page works');
$page = PlanetPage::openBy($I);
$I->seeInTitle('Planets');

$I->wantTo('ensure Satellite page works');
$page = SatellitePage::openBy($I);
$I->seeInTitle('Satellite');