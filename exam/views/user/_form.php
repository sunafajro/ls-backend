<?php

/**
 * @var View     $this
 * @var UserForm $userForm
 * @var array    $roles
 * @var bool     $isNewModel
 */

use exam\models\forms\UserForm;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\web\View;
?>
<?php $form = ActiveForm::begin(); ?>
    <?= $form->field($userForm, 'name')->textInput() ?>
    <?= $form->field($userForm, 'login')->textInput() ?>
    <?php if ($isNewModel) { ?>
        <?= $form->field($userForm, 'pass')->passwordInput() ?>
        <?= $form->field($userForm, 'pass_repeat')->passwordInput() ?>
    <?php } ?>
    <?= $form->field($userForm, 'status')->dropDownList($roles) ?>
    <?= Html::submitButton($isNewModel ? 'Создать' : 'Изменить', ['class' => 'btn ' . ($isNewModel ? 'btn-success' : 'btn-primary')]) ?>
<?php ActiveForm::end(); ?>

