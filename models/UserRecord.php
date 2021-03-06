<?php

namespace app\models;

use yii\db\ActiveRecord;
use  Yii;

class UserRecord extends  ActiveRecord
{
  public  static function tableName()
  {
    return "user";
  }

  public function setTestUser()
  {
    $faker=\Faker\Factory::create();
    $this->name = $faker->name;
    $this->email=$faker->email;
    $this->setPassword($faker->password);
    $this->status=2;
  }

  public static function existsEmail($email)
  {
    $count=static::find()->where(['email'=>$email])->count();
    return $count >0;
  }

  public static function findUserByEmail($email)
  {
    return $count=static::findOne(['email'=>$email]);
  }

  public function setUserJoinForm(UserJoinForm $userJoinForm)
  {
    $this->name=$userJoinForm->name;
    $this->email=$userJoinForm->email;
    $this->setPassword($userJoinForm->password);
    $this->status=1;
  }

  public function setPassword($password)
  {
    $this->passhash=Yii::$app->security->generatePasswordHash($password);
    $this->authokey=Yii::$app->security->generateRandomString(100);
  }

  public function validatePassword($password)
  {
    return Yii::$app->security->validatePassword($password, $this->passhash);
  }
}
