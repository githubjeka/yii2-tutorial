<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'О нас';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Это статическая страница, которая может быть изменена в файле:</p>

    <code><?= __FILE__ ?></code>
</div>