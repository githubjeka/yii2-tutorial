<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Star */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="star-form">

    <?php $form = ActiveForm::begin(['layout' => 'inline']); ?>

    <?= $form->field($model, 'name')->textInput(
        ['maxlength' => 255, 'placeholder' => 'Введите название звезды']
    )?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Создать' : 'Изменить',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
