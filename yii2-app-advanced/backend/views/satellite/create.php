<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Satellite */

$this->title = 'Создание спутника';
$this->params['breadcrumbs'][] = ['label' => 'Спутники', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="satellite-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
