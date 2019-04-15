<?php
    use yii\bootstrap\NavBar;
    use yii\bootstrap\Nav;
?>
<?php $this->beginPage(); ?>
<html lang="ru">
<head>
  <link rel="stylesheet" href="../../public/css/materialize.min.css">
  <meta lang="ru">
  <title> примерная ЗП продавца</title>
    <?php $this->head(); ?>
</head>
<body>
<?php $this->beginBody() ?>
<?php
    NavBar::begin([
            'brandLabel'=>'Расчет ЗП менеджера',
            'brandUrl'=> Yii::$app->homeUrl,
            'options' => [
                    'class'=>'navbar-default navbar-fixed-top'
            ]
    ]);
    $menu=[
            ['label'=>'Войти', 'url'=>['/site/join']],
            ['label'=>'Зарегистрироваться', 'url'=>['/site/login']],
    ];
    echo Nav::widget([
            'options'=>['class'=>'navbar-nav navbar-right'],
            'items'=>$menu
    ]);
    NavBar::end();
?>
<div class="container" style="margin-top: 70px">

    <?=
        $content
    ?>
</div>

<script href="../../public/js/materialize.min.js"></script>
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
