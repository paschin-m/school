<?php

namespace app\controllers;

use Yii;
use app\rbac\UserRoleRule;
use app\models\UserRecord;
use yii\console\Controller;

 class AppconsoleController  extends Controller
 {
   public function actionInitRbac($id=null)
   {
     $auth = Yii::$app->getAuthManager();
     $auth->removeAll();

     $userRoleRule = new UserRoleRule;
     $auth->add($userRoleRule);

     $superuser = $auth->createRole(UserRecord::ROLE_SUPERUSER);
     $superuser->ruleName = $userRoleRule->name;
     $auth->add($superuser);
     $registered = $auth->createRole(UserRecord::ROLE_REGISTERED);
     $registered->ruleName = $userRoleRule->name;
     $auth->add($registered);
     $guest = $auth->createRole(UserRecord::ROLE_GUEST);
     $guest->ruleName = $userRoleRule->name;
     $auth->add($guest);

     $auth->addChild($registered, $guest);
     $auth->addChild($superuser, $registered);

   }
 }
