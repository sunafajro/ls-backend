<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Studphone */
/* @var $form yii\widgets\ActiveForm */
$types = ['1'=>Yii::t('app', 'Mobile'), '2'=>Yii::t('app', 'Home')];
?>

<div class="calc-studphone-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList($items=$types, ['prompt'=>Yii::t('app', '-select-')]) ?>

    <?php
        if($model->isNewRecord){
            echo $form->field($model, 'visible')->hiddenInput(['value'=>1])->label(false);
        }
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
