<?php

namespace app\models;

use yii\base\Model;

class UserLoginForm extends Model
{
  public $email;
  public $password;

  public function rules()
  {
    return [
            ['email','reqired'],
            ['password','reqired'],
            ['email','email']
        ];
  }
}
