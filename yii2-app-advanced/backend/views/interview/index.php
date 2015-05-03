<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Опрос';
$this->params['breadcrumbs'][] = $this->title;
$planets = ['Меркурий', 'Венера', 'Земля', 'Марс', 'Юпитер', 'Сатурн', 'Уран', 'Нептун'];
$astronauts = [
    'Юрий Гагарин',
    'Алексей Леонов',
    'Нил Армстронг',
    'Валентина Терешкова',
    'Эдвин Олдрин',
    'Анатолий Соловьев'
];
?>
<div class="interview-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            [
                'attribute' => 'sex',
                'value' => function ($model) {
                    return $model->sex ? 'Мужчина' : 'Женщина';
                }
            ],
            [
                'attribute' => 'planets',
                'value' => function ($model) use ($planets) {
                    $result = null;
                    $numbers = explode(',', $model->planets);
                    foreach ($numbers as $number) {
                        $result .= $planets[$number] . ' ';
                    }
                    return $result;
                }
            ],
            [
                'attribute' => 'astronauts',
                'value' => function ($model) use ($astronauts) {
                    $result = null;
                    $numbers = explode(',', $model->astronauts);
                    foreach ($numbers as $number) {
                        $result .= $astronauts[$number] . ' ';
                    }
                    return $result;
                }
            ],
            [
                'attribute' => 'planet',
                'value' => function ($model) use ($planets) {
                    return $planets[$model->planet];
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
