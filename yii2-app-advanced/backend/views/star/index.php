<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SearchStar */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Звёзды';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="star-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Star', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                'id',
                'name',
                [
                    'label' => 'Количество планет',
                    'attribute' => 'countPlanets',
                    'value' => function($planet) {
                        return $planet->getPlanets()->count();
                    }
                ],
                ['class' => 'yii\grid\ActionColumn'
            ],
        ],
    ]); ?>

</div>
