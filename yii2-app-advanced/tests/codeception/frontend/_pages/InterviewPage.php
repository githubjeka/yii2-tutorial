<?php

namespace tests\codeception\frontend\_pages;

use tests\codeception\frontend\FunctionalTester;
use \yii\codeception\BasePage;

/**
 * Описывает страницу формы "Опрос"
 */
class InterviewPage extends BasePage
{
    public $route = 'site/interview';

    /**
     * @var FunctionalTester the testing guy object
     */
    protected $actor;

    public function submit(array $formData)
    {
        foreach ($formData as $field => $value) {
            if ($field === 'name' || $field === 'verifyCode' || $field === 'sex') {
                $this->actor->fillField('input[name="Interview[' . $field . ']"]', $value);
            } elseif ($field === 'planets') {
                foreach ($value as $val) {
                    $this->actor->checkOption('input[name="Interview[' . $field . '][]"][value=' . $val . ']');
                }
            } else {
                $this->actor->selectOption('[name="Interview[' . $field . ']"]', $value);
            }
        }

        $this->actor->click('interview-submit');
    }
}
