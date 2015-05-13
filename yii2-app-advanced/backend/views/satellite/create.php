<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Satellite */

$this->title = 'Create Satellite';
$this->params['breadcrumbs'][] = ['label' => 'Satellites', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="satellite-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
