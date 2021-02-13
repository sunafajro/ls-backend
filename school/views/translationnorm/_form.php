<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model school\models\Translationnorm */
/* @var $form yii\widgets\ActiveForm */

$types = ['1'=>Yii::t('app', 'Written'), '2'=>Yii::t('app','Oral'), '3'=>Yii::t('app','Other')]
?>

<div class="translationnorm-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList($items=$types) ?>

    <?= $form->field($model, 'value')->textInput(['readonly' => !$model->isNewRecord]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
