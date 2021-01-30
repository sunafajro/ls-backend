<?php

/**
 * @var View $this
 * @var EducationLevel $model
 * @var ActiveForm $form
 */

use app\models\EducationLevel;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
?>
<div class="edulevel-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'name')->textInput() ?>
    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord
                ? Yii::t('app', 'Add')
                : Yii::t('app', 'Update'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
