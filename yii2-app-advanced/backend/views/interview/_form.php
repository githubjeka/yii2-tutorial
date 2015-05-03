<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Interview */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="interview-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'sex')->checkbox() ?>

    <?= $form->field($model, 'planets')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'astronauts')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'planet')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
