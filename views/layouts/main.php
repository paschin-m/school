<?php
    use yii\bootstrap\NavBar;
?>
<html>
<head>
  <link rel="stylesheet" href="../../public/css/materialize.min.css">
  <meta lang="ru">
  <title> примерная ЗП продавца</title>
</head>
<body>
<?php
    NavBar::begin([
            'brandLabel'=>'Расчет ЗП менеджера',
            'brandUrl'=> Yii::$app->homeUrl,
            'options' => [
                    'class'=>'navbar-default navbar-fixed-top'
            ]
    ]);
    NavBar::end();
?>

<?=
 $content
?>


<script href="../../public/js/materialize.min.js"></script>
</body>
</html>
