<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';
$config=require_once __DIR__ . '/config/console.php';

(new yii\console\Application($config))->run();
