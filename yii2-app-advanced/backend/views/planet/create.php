<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Planet */

$this->title = 'Create Planet';
$this->params['breadcrumbs'][] = ['label' => 'Planets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="planet-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
