<?php

namespace app\models;
use Yii;
use yii\base\Model;

class UserLoginForm extends Model
{
  public $email;
  public $password;

  private $userRecord;

  public function rules()
  {
    return [
            ['email','required','message'=>'поле не может быть пустым'],
            ['password','required','message'=>'поле не может быть пустым'],
            ['email','email','message'=>'совсем охуел человек ебаный!'],
            ['email','errorIfEmailNotFound'], #собственная проверка почты, а что если нет никакой почты!?
            ['password','errorIfPasswordWrong','message'=>'не верный пароль для входа']
        ];
  }

  #реализация валидации поиска по почте
  public function errorIfEmailNotFound()
  {
    $this->userRecord =UserRecord::findUserByEmail($this->email);
    if($this->userRecord==null)
      $this->addError("email", "Пользователь с таким почтовым ящиком не зарегистрирован");
  }

  public function errorIfPasswordWrong()
  {
    if($this->hasErrors())
      return ;
    if ($this->userRecord->passhash!=$this->password)
      $this->addError('password','Не верный пароль пользователя');
  }

  public function login()
  {
    if($this->hasErrors())
      return ;
    $userIdentity=UserIdentity::findIdentity($this->userRecord->id);
    Yii::$app->user->login($userIdentity);
  }
}
