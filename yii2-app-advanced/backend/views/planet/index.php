<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SearchPlanet */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Планеты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="planet-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить планету', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute'=>'name',
                'label'=>'Планета',
            ],
            [
                'label'=>'Звезда',
                'attribute'=>'star_id',
                'value' => function($planet) {
                    return $planet->star->name;
                }
            ],
            [
                'label'=>'Количество спутников',
                'attribute'=>'countSatellites',
                'value' => function($planet) {
                    return $planet->getSatellites()->count();
                }
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
