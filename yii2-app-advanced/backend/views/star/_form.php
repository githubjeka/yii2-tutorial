<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm; //заменить на yii\bootstrap\ActiveForm, если планируете использовать ['layout' => 'horizontal',]

/* @var $this yii\web\View */
/* @var $model common\models\Star */
/* @var $form ActiveForm */
?>

<div class="star-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
