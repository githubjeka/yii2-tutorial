<?php
if (basename($_SERVER['DOCUMENT_ROOT']) !== 'yii2-tutorial') {
    exit('Сервер PHP запущен не из той директории. Выполните "php -S localhost:9000" из "yii2-tutorial"');
}


$steps = [
    'main' => [
        'title' => 'Учебник',
        'file' => 'main.md',
    ],
    'step-0' => [
        'title' => 'Знакомимся с приложением',
        'file' => 'step-000.md',
    ],
    'step-0.1' => [
        'title' => 'Виды и шаблоны',
        'file' => 'step-000.1.md',
    ],
    'step-0.2' => [
        'title' => 'Формы',
        'file' => 'step-000.2.md',
    ],
    'step-1' => [
        'title' => 'Генерация модели',
        'file' => 'step-001.md',
    ],
    'step-1.1' => [
        'title' => 'Редактирование модели',
        'file' => 'step-001.1.md',
    ],
    'step-1.2' => [
        'title' => 'Отображение моделей',
        'file' => 'step-001.2.md',
    ],
    'step-2' => [
        'title' => 'Создание связанных моделей',
        'file' => 'step-002.md',
    ],
    'step-2.1' => [
        'title' => 'Отображение связанных моделей',
        'file' => 'step-002.1.md',
    ],
];

if (isset($_GET['c'], $steps[$_GET['c']])) {
    $step = $steps[$_GET['c']];
} else {
    $step = $steps['main'];
}

require_once __DIR__ . '/vendor/autoload.php';

$markdown = file_get_contents(__DIR__ . '/steps/' . $step['file']);
$parser = new \cebe\markdown\GithubMarkdown();
?>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Учебник по Yii2: <?= $step['title'] ?></title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootswatch/3.3.4/readable/bootstrap.min.css">
</head>
<body class="container">

<div class="row">

    <div class="col-md-9 col-lg-8">
        <?= $parser->parse($markdown); ?>
    </div>
    <div class="col-md-3 col-lg-4">
        <ul class="nav">
            <?php foreach ($steps as $key => $_step) { ?>
                <li>
                    <a href="/scripts/index.php?c=<?= $key ?>"><?= $_step['title'] ?></a>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>
<hr>
<footer>

</footer>

<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
</body>

</html>

