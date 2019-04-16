<?php
      use yii\helpers\Html;
      use yii\widgets\ActiveForm;

      ?>
<div class="panel panel-info">
    <div class="panel-heading  red-text">
        <h1>Регистрация в системе:</h1>
    </div>
    <div class="panel-body">
      <?php $form=ActiveForm::begin(['id'=>'user-join-form']);?>
      <?= $form->field($userLoginForm, 'email')->label('Введите e-mail') ?>
      <?= $form->field($userLoginForm, 'password')->label('Введите пароль')->passwordInput() ?>

      <?= Html::submitButton('Войти',['class'=>'btn btn-primary']) ?>
      <?php ActiveForm::end(); ?>
    </div>
</div>
