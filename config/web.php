<?php


  return $config=[
      'id'=>'school',
      'basePath' =>realpath(__DIR__.'/../'),
      'components' => [
        'assetManager' => [
                          'class' => 'yii\web\AssetManager',
                          'appendTimestamp' => true,
        'bundles' => [
                'romdim\bootstrap\material\BootMaterialCssAsset' => [
                                                                    'css' => [
                                                                              YII_ENV_DEV ? 'css/ripples.css' : 'css/ripples.min.css',
                                                                              YII_ENV_DEV ? 'css/material.css' : 'css/material.min.css',
                                                                            ]
                                                                    ],
                'romdim\bootstrap\material\BootMaterialJsAsset' => [
                                                                    'js' => [
                                                                        YII_ENV_DEV ? 'js/ripples.js' : 'js/ripples.min.js',
                                                                        YII_ENV_DEV ? 'js/material.js' : 'js/material.min.js',
                                                                    ]
                                                                  ]
                    ]
                  ]

                ]

  ];
