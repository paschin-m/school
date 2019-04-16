<?php

namespace app\controllers;

 use app\models\UserIdentity;
 use app\models\UserJoinForm;
 use app\models\UserRecord;
 use yii\web\Controller;
 use app\models\UserLoginForm;
 use Yii;

class UserController extends Controller
{
  public function actionJoin()
  {
    #разделим на GET и  POST данные
    if (Yii::$app->request->isPost)
      return $this->actionJoinPost();

    $userJoinForm=new UserJoinForm();
    $userRecord=new UserRecord();
    $userRecord->setTestUser();
    $userJoinForm->setUserRecord($userRecord);

    return $this->render('join', compact('userJoinForm'));
  }

  /**
   * @return string
   */
  public function actionJoinPost()
  {
   $userJoinForm=new UserJoinForm();
   if ($userJoinForm->load(Yii::$app->request->post()))
     if ($userJoinForm->validate())
     {
         $userRecord=new UserRecord();
         $userRecord->setUserJoinForm($userJoinForm);
         $userRecord->save();
         return $this->redirect('index.php?r=user/login'); #render() работать не будет всилу из-за возможного нажатия F5 и обновления страницы
     }
   return $this->render('join', compact('userJoinForm'));
  }

  public function actionLogin()
  {
    if(Yii::$app->request->isPost)
      return $this->actionLoginPost();
    $userLoginForm=new UserLoginForm();

    return $this->render('login', compact('userLoginForm')); # на вход передаем имена переменных объекта формы входа в виде массива
  }

  public function actionLoginPost()
  {
    $userLoginForm=new UserLoginForm();
    if ($userLoginForm->load(Yii::$app->request->post()))
      if($userLoginForm->validate())
      {
        $userLoginForm->login();
        return $this->redirect("index.php?r=site/index");
      }
    return $this->render('login', compact('userLoginForm')); # на вход передаем имена переменных объекта формы входа в виде массива
  }
  public function actionLogout()
  {

    Yii::$app->user->logout();
    return $this->render('login'); # на вход
  }

}
