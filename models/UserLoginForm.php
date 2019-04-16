<?php

namespace app\models;
use Yii;
use yii\base\Model;

class UserLoginForm extends Model
{
  public $email;
  public $password;

  public function rules()
  {
    return [
            ['email','required','message'=>'поле не может быть пустым'],
            ['password','required','message'=>'поле не может быть пустым'],
            ['email','email','message'=>'совсем охуел человек ебаный!'],
            ['email','errorIfEmailNotFound'] #собственная проверка почты, а что если нет никакой почты!?
        ];
  }

  #реализация валидации поиска по почте
  public function errorIfEmailNotFound()
  {
    $userRecord=UserRecord::findUserByEmail($this->email);
    if($userRecord->email!=$this->email)
      $this->addError("email", "Пользователь с таким почтовым ящиком не зарегистрирован");
  }

  public function login()
  {
    if($this->hasErrors())
      return ;
    $userRecord=UserRecord::findUserByEmail($this->email);
    $userIdentity=UserIdentity::findIdentity($userRecord->id);
    Yii::$app->user->login($userIdentity);
  }
}
