<?php

//$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';


  $config= [
      'id'=>'school',
      'basePath' =>realpath(__DIR__.'/../'),
      'bootstrap'=>[
          'log', 'debug'
      ],
      'components' => [
          'urlManager'=> [
              'enablePrettyUrl'=> false,
              'showScriptName'=>false
          ],
          'db'=> $db,
          'user'=>[
              'identityClass'=>'app\models\UserIdentity'
          ],

        'request'=>[
            'cookieValidationKey' => 'qBbuejR7ap0rtcbu__SvWeHOb64NhLDB',
        ],
          'log' => [
              'traceLevel' => YII_DEBUG ? 3 : 0,
              'targets' => [
                  [
                      'class' => 'yii\log\SyslogTarget',
                      'levels' => ['error', 'warning'],
                  ],
              ],
          ],
      ],
     'modules'=> [
         'debug' => [
             'class' =>'yii\debug\Module',
             'allowedIPs' => ['127.0.0.1', '::1']
         ]
     ],

  ];

  return $config;
