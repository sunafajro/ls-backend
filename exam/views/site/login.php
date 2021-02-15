<?php

/**
 * @var View      $this
 * @var LoginForm $model
 * @var ActiveForm $form
 */

use exam\models\forms\LoginForm;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\web\View;

$this->title = Yii::$app->name;
?>
<div class="site-login">
    <div class="row justify-content-center">
        <div class="col-4">
            <p class="text-center"><?php echo Yii::t('app','Please fill out the following fields to login:'); ?></p>
            <?php $form = ActiveForm::begin() ?>
            <?= $form->field($model, 'username')->textInput() ?>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <div class="form-group">
                <div class="text-center">
                    <?= Html::submitButton(Yii::t('app','Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
