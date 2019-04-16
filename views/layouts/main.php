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
    if (Yii::$app->user->isGuest) {
      $menu = [
          ['label' => 'Зарегистрироваться', 'url' => ['/user/join']],
          ['label' =>'Войти' , 'url' => ['/user/login']],
      ];
    }
    else {
      $menu=[
          ['label'=>Yii::$app->user->getIdentity()->name],
          ['label'=>'Выйти', 'url'=>['/user/logout']],
      ];
    }
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
