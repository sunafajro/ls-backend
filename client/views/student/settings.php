<?php

/**
 * @var yii\web\View                  $this
 * @var yii\bootstrap\ActiveForm      $formUsername
 * @var yii\bootstrap\ActiveForm      $formPassword
 * @var client\models\ChangeUsernameForm $changeUsername
 * @var client\models\ChangePasswordForm $changePassword
 */

use common\widgets\alert\AlertWidget;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::$app->params['siteTitle'];
?>
<div class="content-block">
    <div class="row">
	    <div class="col-xs-12 col-sm-8">
            <?= AlertWidget::widget() ?>
            <h3><?= Yii::t('app', 'Change login parameters') ?></h3>
            <?php $formUsername = ActiveForm::begin([
                'id' => 'change-username-form',
            ]); ?>
                <?= $formUsername->field($changeUsername, 'username')->textInput() ?>
                <?= $formUsername->field($changeUsername, 'username_repeat')->textInput() ?>
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
                </div>
            <?php ActiveForm::end(); ?>
            <hr />
            <?php $formPassword = ActiveForm::begin([
                'id' => 'change-password-form',
            ]); ?>
                <?= $formPassword->field($changePassword, 'password')->passwordInput() ?>
                <?= $formPassword->field($changePassword, 'password_repeat')->passwordInput() ?>
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
	    <div class="col-xs-12 col-sm-4">
            <blockquote>
                <p>Логин должен соотвествовать следующим параметрам:</p>
                <ul>
                    <li>Минимум 5 символов</li>
                    <li>Максимум 20 символов</li>
                    <li>Разрешены буквы, цифры и подчеркивание</li>
                </ul>
                <p>Пароль должен соотвествовать следующим параметрам:</p>
                <ul>
                    <li>Минимум 8 символов</li>
                    <li>Максимум 20 символов</li>
                </ul>
            </blockquote>
        </div>
    </div>
</div>