<?php

namespace app\models;

use yii\db\ActiveRecord;

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
    $this->passhash=$faker->password;
    $this->status=2;
  }

  public static function existsEmail($email)
  {
    $count=static::find()->where(['email'=>$email])->count();
    return $count >0;
  }
}
