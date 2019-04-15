<?php
 namespace app\controllers;
use yii\web\Controller;

class UserController extends Controller
{
  public function actionJoin()
  {
    return $this->render('join');
  }

  public function actionLogin()
  {
    return $this->render('login');
  }

}
