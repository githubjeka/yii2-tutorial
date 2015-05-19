<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Star */

$this->title = 'Создание звезды';
$this->params['breadcrumbs'][] = ['label' => 'Звёзды', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="star-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
