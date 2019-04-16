<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>
<div class="panel panel-info">
    <div class="panel-heading  red-text">
        <h1>Зарегистрироваться в системе:</h1>
    </div>
    <div class="panel-body">
      <?php $form=ActiveForm::begin(['id'=>'user-join-form']); ?>
        <?= $form->field($userJoinForm, 'name')->label('Введите имя пользователя системы') ?>
      <?= $form->field($userJoinForm, 'password')->label('Введите пароль')->passwordInput() ?>
      <?= $form->field($userJoinForm, 'password2')->label('Повторите пароль')->passwordInput() ?>
      <?= $form->field($userJoinForm, 'email')->label('Введите e-mail') ?>
      <?= Html::submitButton('Зарегистрироваться',['class'=>'btn btn-danger']) ?>
      <?php ActiveForm::end(); ?>
    </div>
</div>

