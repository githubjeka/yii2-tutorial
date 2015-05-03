<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Interview */

$this->title = 'Create Interview';
$this->params['breadcrumbs'][] = ['label' => 'Interviews', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="interview-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
