<?php

namespace app\controllers;

 use app\models\UserIdentity;
 use app\models\UserJoinForm;
 use app\models\UserRecord;
 use yii\web\Controller;
 use Yii;

class UserController extends Controller
{
  public function actionJoin()
  {
   # $userRecord= new UserRecord();
   # $userRecord->setTestUser();
   # $userRecord->save();
    $userJoinForm=new UserJoinForm();
    return $this->render('join',['userJoinForm'=>$userJoinForm]);
  }

  public function actionLogin()
  {
  //  $uid=UserIdentity::findIdentity(1);
  //  Yii::$app->user->login($uid);

    return $this->render('login');
  }

  public function actionLogout()
  {

    Yii::$app->user->logout();
    return $this->render('login');
  }

}
