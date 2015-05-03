<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Interview */

$this->title = 'Update Interview: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Interviews', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="interview-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
