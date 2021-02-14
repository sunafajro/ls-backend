<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model school\models\Langteacher */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="calc-langteacher-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'calc_teacher')->hiddenInput(['value'=>$teacher['tid']])->label(false) ?>

    <?= $form->field($model, 'calc_lang')->dropDownList($items=$langs,['prompt'=>\Yii::t('app','-select-')])->label(\Yii::t('app','Language')) ?>

    <?= $form->field($model, 'visible')->hiddenInput(['value'=>1])->label(false) ?>

    <?= $form->field($model, 'data')->hiddenInput(['value'=>date('Y-m-d')])->label(false) ?>

    <?= $form->field($model, 'user')->hiddenInput(['value'=>Yii::$app->session->get('user.uid')])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app','Create') : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
