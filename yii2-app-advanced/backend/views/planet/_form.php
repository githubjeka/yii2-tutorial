<?php

use common\models\Star;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Planet */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="planet-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal',]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Введите название']) ?>

    <?= $form->field($model, 'star_id')->dropDownList(ArrayHelper::map(Star::find()->all(), 'id', 'name'))->label(
        'Звезда'
    ) ?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Создать' : 'Изменить',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
