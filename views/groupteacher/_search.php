<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CalcGroupteacherSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="calc-groupteacher-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'calc_teacher') ?>

    <?= $form->field($model, 'calc_service') ?>

    <?= $form->field($model, 'calc_office') ?>

    <?= $form->field($model, 'calc_edulevel') ?>

    <?php // echo $form->field($model, 'data') ?>

    <?php // echo $form->field($model, 'user') ?>

    <?php // echo $form->field($model, 'data_visible') ?>

    <?php // echo $form->field($model, 'user_visible') ?>

    <?php // echo $form->field($model, 'visible') ?>

    <?php // echo $form->field($model, 'corp') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
