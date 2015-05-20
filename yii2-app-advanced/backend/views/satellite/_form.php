<?php

use common\models\Planet;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Satellite */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="satellite-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Введите название спутника']) ?>

    <?= $form->field($model, 'planet_id')
        ->dropDownList(\yii\helpers\ArrayHelper::map(Planet::find()->all(), 'id', 'name'))
        ->label('Название планеты')
        ->hint('У этой планеты появится новый спутник.') ?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Создать' : 'Изменить',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
