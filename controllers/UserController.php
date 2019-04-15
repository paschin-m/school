<?php

namespace app\controllers;

 use app\models\UserRecord;
 use yii\web\Controller;

class UserController extends Controller
{
  public function actionJoin()
  {
    $userRecord= new UserRecord();
    $userRecord->setTestUser();
    $userRecord->save();

    return $this->render('join');
  }

  public function actionLogin()
  {
    return $this->render('login');
  }

}
