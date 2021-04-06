<?php

/**
 * @var View $this
 * @var AccessRule $model
 * @var ActiveForm $form
 */

use school\models\AccessRule;
use school\models\Role;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

$roles = Role::find()->select('name')->indexBy('id')->orderBy('id')->column();
?>
<div class="role-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'controller')->textInput() ?>
    <?= $form->field($model, 'action')->textInput() ?>
    <?= $form->field($model, 'role_id')->dropDownList($roles, ['prompt' => Yii::t('app', '-select-')]) ?>
    <?= $form->field($model, 'user_id')->Input('number') ?>
    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord
                ? Yii::t('app', 'Add')
                : Yii::t('app', 'Update'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>