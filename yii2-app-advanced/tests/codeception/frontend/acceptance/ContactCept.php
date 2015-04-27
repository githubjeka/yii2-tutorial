<?php
use tests\codeception\frontend\AcceptanceTester;
use tests\codeception\frontend\_pages\ContactPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that contact works');

$contactPage = ContactPage::openBy($I);

$I->see('Обратная связь', 'h1');

$I->amGoingTo('submit contact form with no data');
$contactPage->submit([]);
if (method_exists($I, 'wait')) {
    $I->wait(3); // only for selenium
}
$I->expectTo('see validations errors');
$I->see('Обратная связь', 'h1');
$I->see('Необходимо заполнить «Ваше имя».', '.help-block');
$I->see('Необходимо заполнить «Email».', '.help-block');
$I->see('Необходимо заполнить «Тема сообщения».', '.help-block');
$I->see('Необходимо заполнить «Текст сообщения».', '.help-block');
$I->see('Неправильный проверочный код.', '.help-block');

$I->amGoingTo('submit contact form with not correct email');
$contactPage->submit([
    'name' => 'tester',
    'email' => 'tester.email',
    'subject' => 'test subject',
    'body' => 'test content',
    'verifyCode' => 'testme',
]);
if (method_exists($I, 'wait')) {
    $I->wait(3); // only for selenium
}
$I->expectTo('see that email adress is wrong');
$I->dontSee('Необходимо заполнить «Ваше имя».', '.help-block');
$I->see('Значение «Email» не является правильным email адресом.', '.help-block');
$I->dontSee('Необходимо заполнить «Тема сообщения».', '.help-block');
$I->dontSee('Необходимо заполнить «Текст сообщения».', '.help-block');
$I->dontSee('Неправильный проверочный код.', '.help-block');

$I->amGoingTo('submit contact form with correct data');
$contactPage->submit([
    'name' => 'tester',
    'email' => 'tester@example.com',
    'subject' => 'test subject',
    'body' => 'test content',
    'verifyCode' => 'testme',
]);
if (method_exists($I, 'wait')) {
    $I->wait(3); // only for selenium
}
$I->see('Спасибо за ваше письмо. Мы свяжемся с вами в ближайшее время.');
