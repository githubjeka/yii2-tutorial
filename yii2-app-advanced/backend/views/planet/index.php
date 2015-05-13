<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SearchPlanet */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Planets';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="planet-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Planet', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'star_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
