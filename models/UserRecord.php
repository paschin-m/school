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
    $this->name = "John";
    $this->email="vvvv@loc.ru";
    $this->passhash="q+++++++++++++++++++++was";
    $this->status=2;
  }

}
