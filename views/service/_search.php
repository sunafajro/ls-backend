<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CalcServiceSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="calc-service-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'visible') ?>

    <?= $form->field($model, 'calc_eduage') ?>

    <?= $form->field($model, 'calc_lang') ?>

    <?= $form->field($model, 'calc_eduform') ?>

    <?php // echo $form->field($model, 'name') ?>

    <?php // echo $form->field($model, 'calc_studnorm') ?>

    <?php // echo $form->field($model, 'data') ?>

    <?php // echo $form->field($model, 'calc_timenorm') ?>

    <?php // echo $form->field($model, 'calc_city') ?>

    <?php // echo $form->field($model, 'calc_servicetype') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
