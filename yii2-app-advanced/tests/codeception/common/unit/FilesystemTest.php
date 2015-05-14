<?php

namespace tests\codeception\common\unit\models;

use Codeception\Module\Filesystem;
use Yii;
use tests\codeception\common\unit\TestCase;
use Codeception\Specify;

/**
 * FilesystemTest form test
 */
class FilesystemTest extends TestCase
{
    use Specify;

    public function testFileConfig()
    {
        /** @var Filesystem $I */
        $I = $this->getModule('Filesystem');

        $this->specify(
            'config file should be correct according to description step',
            function () use ($I) {
                $I->amInPath(Yii::getAlias('@common/config'));
                $I->openFile('main.php');
                $I->seeInThisFile("'name' => 'Мой сайт',");
                $I->seeInThisFile("'language' => 'ru',");
            }
        );

        $this->specify(
            'site controller file must contain necessary code',
            function () use ($I) {
                $I->amInPath(Yii::getAlias('@frontend/controllers'));
                $I->openFile('SiteController.php');
                $I->seeInThisFile("'class' => 'yii\web\ViewAction',");
                $I->seeInThisFile(<<<'CODE'
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
CODE
                );
            }
        );

        $this->specify(
            'view of contact form must contain necessary code',
            function () use ($I) {
                $I->amInPath(Yii::getAlias('@frontend/views/site'));
                $I->openFile('contact.php');
                $I->seeInThisFile(<<<'CODE'
            <?php $form = ActiveForm::begin(['id' => 'contact-form', 'enableClientValidation' => false]); ?>
            <?= $form->field($model, 'name') ?>
            <?= $form->field($model, 'email') ?>
            <?= $form->field($model, 'subject') ?>
            <?= $form->field($model, 'body')->textArea(['rows' => 6]) ?>
            <?= $form->field($model, 'verifyCode')->widget(
                Captcha::className(),
                [
                    'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
                ]
            ) ?>
            <div class="form-group">
                <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
CODE
                );
            }
        );

        $this->specify(
            'model of contact form must contain necessary code',
            function () use ($I) {
                $I->amInPath(Yii::getAlias('@frontend/models'));
                $I->openFile('ContactForm.php');
                $I->seeInThisFile('public $name;');
                $I->seeInThisFile('public $email;');
                $I->seeInThisFile('public $subject;');
                $I->seeInThisFile('public $verifyCode;');

                $I->seeInThisFile('public function rules()');
                $I->seeInThisFile('public function attributeLabels()');
                $I->seeInThisFile('public function sendEmail($email)');
            }
        );

        $this->specify(
            'migration m150428_104828_interview.php should be create',
            function () use ($I) {
                $I->seeFileFound('m150428_104828_interview.php', Yii::getAlias('@console/migrations'));
            }
        );
    }
}
