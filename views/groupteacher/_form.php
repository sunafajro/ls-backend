<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CalcGroupteacher */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="calc-groupteacher-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php 
        // $form->field($model, 'calc_teacher')->hiddenInput(['value'=>$teacher['id']])->label(false);
    ?>

    <?= $form->field($model, 'calc_service')->dropDownList($items=$services, ['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'calc_edulevel')->dropDownList($items=$levels, ['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'calc_office')->dropDownList($items=$offices, ['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'company')->dropDownList($items=$jobPlace, ['prompt'=>Yii::t('app', '-select-')]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Add') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
