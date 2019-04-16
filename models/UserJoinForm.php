<?php

namespace app\models;

use yii\base\Model;

class UserJoinForm extends Model
{
  public $name;
  public $email;
  public $password;
  public $password2;

  public function rules()
  {
    return [
        ['name','required','message'=>'обязательное поле введите данные'],
        ['email','required','message'=>'обязательное поле введите данные'],
        ['password','required','message'=>'обязательное поле введите данные'],
        ['password2','required','message'=>'обязательное поле введите данные'],
        ['name','string', 'min'=>3,'max'=>45,'message'=>'вы превысили лимит, возможно не бывает таких длинных имен!'],
        ['email','email','message'=>'Здесь только адрес электронной почты!'],
        ['password','string','min'=>4],
        ['password2','compare','compareAttribute'=>'password','message'=>'Пароли не совпадают']
    ];
  }
  public function setUserRecord($userRecord)
  {
    $this->name=$userRecord->name;
    $this->email=$userRecord->email;
    $this->password=$this->password2="qwas";

  }
}
