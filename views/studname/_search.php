<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CalcStudnameSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="calc-studname-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'visible') ?>

    <?= $form->field($model, 'history') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'debt') ?>

    <?php // echo $form->field($model, 'invoice') ?>

    <?php // echo $form->field($model, 'money') ?>

    <?php // echo $form->field($model, 'calc_sex') ?>

    <?php // echo $form->field($model, 'calc_cumulativediscount') ?>

    <?php // echo $form->field($model, 'active') ?>

    <?php // echo $form->field($model, 'calc_way') ?>

    <?php // echo $form->field($model, 'description') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
