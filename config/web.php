<?php
use app\models\UserRecord;

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
              'identityClass'=>'app\models\UserIdentity',
              'enableAutoLogin'=>true
          ],

        'request'=>[
            'cookieValidationKey' => 'qBbuejR7ap0rtcbu__SvWeHOb64NhLDB',
        ],
          'authManager' => [
              'class' => 'yii\rbac\PhpManager',
              'itemFile' => '@app/rbac/items.php',
              'ruleFile' => '@app/rbac/rules.php',
              'assignmentFile' => '@app/rbac/assignments.php', // назначения придется указать, потому что того требуют каноны церкви
              'defaultRoles' => UserRecord::roleArray(),
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
