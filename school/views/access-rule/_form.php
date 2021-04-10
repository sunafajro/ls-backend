<?php

/**
 * @var View $this
 * @var AccessRule $model
 * @var ActiveForm $form
 */

use school\models\AccessRule;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
?>
<div class="role-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'slug')->textInput() ?>
    <?= $form->field($model, 'name')->textInput() ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord
                ? Yii::t('app', 'Add')
                : Yii::t('app', 'Update'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>