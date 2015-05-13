<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SearchSatellite */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Satellites';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="satellite-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Satellite', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'planet_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
